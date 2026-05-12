<?php

namespace Modules\AI\Drivers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\AI\Interfaces\GeminiDriverInterface;
use Google\Auth\Credentials\ServiceAccountCredentials;

/**
 * Vertex AI Driver with Context Caching
 *
 * Flow:
 *  1. On first request (or cache expired): Build full product catalog from DB,
 *     upload to Vertex AI as a CachedContent, store the cache name in Laravel Cache.
 *  2. On every subsequent request: Only send the user question + cache reference.
 *     The AI answers using the cached catalog — no need to re-send product data!
 *
 * Cost savings: cached tokens are billed at ~25% of normal input token price.
 *
 * Prerequisites:
 *  - composer require google/auth
 *  - AI_PROVIDER=vertex in .env
 *  - VERTEX_PROJECT_ID, VERTEX_LOCATION, GOOGLE_APPLICATION_CREDENTIALS set
 */
class VertexAIDriver implements GeminiDriverInterface
{
    protected array  $personas;
    protected string $projectId;
    protected string $location;
    protected string $model;
    protected string $embeddingModel;
    protected string $credentialsPath;

    // Laravel Cache key for storing the Vertex cache name
    const CACHE_KEY_PREFIX = 'vertex_context_cache_';

    // Vertex AI cache TTL in seconds (1 hour max per API limit)
    const VERTEX_CACHE_TTL = 3600;

    // Laravel cache TTL (slightly shorter to refresh before Vertex cache expires)
    const LARAVEL_CACHE_TTL_MINUTES = 55;

    public function __construct(array $personas)
    {
        $this->personas        = $personas;
        $this->projectId       = config('ai.vertex.project_id');
        $this->location        = config('ai.vertex.location', 'asia-southeast1');
        $this->model           = config('ai.vertex.model', 'gemini-2.0-flash-001');
        $this->embeddingModel  = config('ai.vertex.embedding_model', 'text-embedding-005');
        $this->credentialsPath = env('GOOGLE_APPLICATION_CREDENTIALS', '');
    }

    // =========================================================================
    // Authentication
    // =========================================================================

    /**
     * Get a short-lived OAuth2 access token from the Service Account JSON file.
     */
    protected function getAccessToken(): string
    {
        if (!file_exists($this->credentialsPath)) {
            throw new \RuntimeException(
                "Service Account file not found: {$this->credentialsPath}. " .
                "Set GOOGLE_APPLICATION_CREDENTIALS in .env"
            );
        }

        $keyData     = json_decode(file_get_contents($this->credentialsPath), true);
        $credentials = new ServiceAccountCredentials(
            ['https://www.googleapis.com/auth/cloud-platform'],
            $keyData
        );
        $token = $credentials->fetchAuthToken();

        if (empty($token['access_token'])) {
            throw new \RuntimeException('Failed to obtain Vertex AI access token.');
        }

        return $token['access_token'];
    }

    // =========================================================================
    // Context Caching
    // =========================================================================

    /**
     * Build the full product catalog text from database.
     * This is what gets cached in Vertex AI — the ENTIRE catalog.
     */
    protected function buildFullCatalog(): string
    {
        $products = \Modules\ProductCatalog\Models\Product::all(
            ['id', 'name', 'slug', 'description', 'sku']
        );

        if ($products->isEmpty()) {
            return 'Belum ada produk di katalog.';
        }

        $catalog = "=== KATALOG PRODUK ACTIV (LENGKAP) ===\n\n";
        foreach ($products as $product) {
            $catalog .= "ID: {$product->id}\n";
            $catalog .= "Nama: {$product->name}\n";
            if ($product->sku)         $catalog .= "SKU: {$product->sku}\n";
            if ($product->description) $catalog .= "Deskripsi: " . strip_tags($product->description) . "\n";
            $catalog .= "Link: /products/{$product->slug}\n";
            $catalog .= "---\n";
        }

        return $catalog;
    }

    /**
     * Create a CachedContent in Vertex AI with the system instruction and full product catalog.
     * Returns the cache resource name (e.g. "projects/.../cachedContents/abc123").
     */
    protected function createVertexCache(string $systemInstruction): ?string
    {
        $token = $this->getAccessToken();

        // Context caching uses v1beta1 endpoint
        $endpoint = "https://{$this->location}-aiplatform.googleapis.com/v1beta1"
                  . "/projects/{$this->projectId}/locations/{$this->location}/cachedContents";

        $modelResource = "projects/{$this->projectId}/locations/{$this->location}"
                       . "/publishers/google/models/{$this->model}";

        $response = Http::withToken($token)
            ->timeout(180)
            ->post($endpoint, [
                'model'             => $modelResource,
                'systemInstruction' => [
                    'parts' => [['text' => $systemInstruction]],
                ],
            ]);


        if ($response->failed()) {
            // Common failure: token count below 32k minimum
            Log::warning('Vertex AI cache creation failed, falling back to non-cached mode.', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return null;
        }

        return $response->json('name');
    }

    /**
     * Get or create the Vertex AI cache for the given persona.
     * Uses Laravel cache to avoid re-uploading on every request.
     *
     * Returns cache name string, or null if caching is not available.
     */
    protected function getOrCreateCache(string $persona): ?string
    {
        $personaData       = $this->personas[$persona] ?? $this->personas['sales'];
        $systemInstruction = $personaData['prompt'];

        $cacheKey = self::CACHE_KEY_PREFIX . $persona . '_' . $this->model;

        // Return from Laravel cache if still valid
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Build full catalog and upload to Vertex AI
        Log::info('Vertex AI: Creating new context cache for persona: ' . $persona);
        // Create the cache (Persona only)
        $cacheName = $this->createVertexCache($systemInstruction);


        if ($cacheName) {
            Cache::put($cacheKey, $cacheName, now()->addMinutes(self::LARAVEL_CACHE_TTL_MINUTES));
            Log::info('Vertex AI: Context cache created.', ['cache_name' => $cacheName]);
        }

        return $cacheName;
    }

    /**
     * Force-refresh the context cache (e.g. when products are updated).
     * Call this after adding/updating products in the catalog.
     */
    public function refreshCache(string $persona = 'sales'): void
    {
        $cacheKey = self::CACHE_KEY_PREFIX . $persona . '_' . $this->model;
        Cache::forget($cacheKey);
        $this->getOrCreateCache($persona);
    }

    // =========================================================================
    // Main AI Methods
    // =========================================================================

    /**
     * Generate a response using context caching.
     * Falls back to non-cached mode if cache is unavailable.
     */
    public function generateResponse(string $userMessage, string $context, string $persona = 'sales'): string
    {
        $token    = $this->getAccessToken();
        $endpoint = "https://{$this->location}-aiplatform.googleapis.com/v1"
                  . "/projects/{$this->projectId}/locations/{$this->location}"
                  . "/publishers/google/models/{$this->model}:generateContent";

        $cacheName = $this->getOrCreateCache($persona);

        if ($cacheName) {
            // ✅ CACHED MODE: Persona from cache, Relevant context from user turn
            $fullPrompt = "DATA KONTEKS PRODUK RELEVAN:\n{$context}\n\nPERTANYAAN USER: {$userMessage}";

            $response = Http::withToken($token)
                ->timeout(180)
                ->post($endpoint, [
                    'cachedContent' => $cacheName,
                    'contents'      => [
                        [
                            'role'  => 'user',
                            'parts' => [['text' => $fullPrompt]],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature'     => 0.7,
                        'maxOutputTokens' => 2048,
                    ],
                    'safetySettings' => [
                        ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE'],
                        ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_NONE'],
                        ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
                        ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE'],
                    ],
                ]);
        }
 else {
            // 🔄 FALLBACK MODE: Send full context (catalog too small to cache / < 32k tokens)
            $personaData       = $this->personas[$persona] ?? $this->personas['sales'];
            $systemInstruction = $personaData['prompt'];
 
            $fullPrompt = "DATA KONTEKS PRODUK:\n{$context}\n\nPERTANYAAN USER: {$userMessage}";
 
            $response = Http::withToken($token)
                ->timeout(180)
                ->post($endpoint, [
                    'systemInstruction' => [
                        'parts' => [['text' => $systemInstruction]],
                    ],
                    'contents' => [
                        [
                            'role'  => 'user',
                            'parts' => [['text' => $fullPrompt]],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature'     => 0.7,
                        'maxOutputTokens' => 2048,
                    ],
                    'safetySettings' => [
                        ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_NONE'],
                        ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_NONE'],
                        ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
                        ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE'],
                    ],
                ]);
        }


        if ($response->failed()) {
            throw new \RuntimeException(
                "Vertex AI Error [{$response->status()}]: " . $response->body()
            );
        }

        return $response->json('candidates.0.content.parts.0.text', '');
    }

    /**
     * Get a vector embedding for a given text using Vertex AI.
     */
    public function getEmbedding(string $text): array
    {
        $token    = $this->getAccessToken();
        $endpoint = "https://{$this->location}-aiplatform.googleapis.com/v1"
                  . "/projects/{$this->projectId}/locations/{$this->location}"
                  . "/publishers/google/models/{$this->embeddingModel}:predict";

        $response = Http::withToken($token)
            ->timeout(180) // Sabar nunggu jawaban 3 menit
            ->post($endpoint, [
                'instances' => [['content' => $text]],
            ]);

        if ($response->failed()) {
            throw new \RuntimeException(
                "Vertex AI Embedding Error [{$response->status()}]: " . $response->body()
            );
        }

        return $response->json('predictions.0.embeddings.values', []);
    }


    /**
     * Summarize a chat history into a short lead summary.
     */
    public function summarizeChat(array $history): string
    {
        try {
            $chatLog = '';
            foreach ($history as $msg) {
                $role    = ($msg['role'] === 'user') ? 'Customer' : 'VION by ACTiV';
                $content = is_array($msg['content']) ? json_encode($msg['content']) : $msg['content'];
                $chatLog .= "{$role}: {$content}\n";
            }

            $summaryPrompt = "Buatkan rangkuman percakapan berikut dalam format pesan WhatsApp yang SANGAT RAPI untuk tim Sales. " .
                             "PENTING: Gunakan format EXACTLY seperti template di bawah ini, jangan tambahkan kata-kata lain di luar template:\n\n" .
                             "Halo Tim Sales ACTiV,\n\n" .
                             "Saya ingin diskusi lebih lanjut terkait prospek yang baru masuk. Berikut adalah detail data yang diteruskan:\n\n" .
                             "* [Sumber: Rangkuman Chat Vion by ACTiV]\n" .
                             "* Kebutuhan Customer:\n" .
                             "[Sebutkan produk dan kebutuhan utama user di sini dalam bentuk list poin pakai simbol -, gunakan bold bintang satu '*' untuk nama produk]\n\n" .
                             "Mohon update untuk ketersediaan (lisensi & _hardware_) serta estimasi penawarannya agar bisa kita diskusikan langkah selanjutnya. \n\n" .
                             "Terima kasih!\n\n" .
                             "LOG PERCAKAPAN:\n{$chatLog}";

            return $this->generateResponse($summaryPrompt, '', 'sales');
        } catch (\Exception $e) {
            return 'Tertarik diskusi lebih lanjut.';
        }
    }
}
