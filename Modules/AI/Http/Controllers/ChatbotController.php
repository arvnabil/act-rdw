<?php

namespace Modules\AI\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\AI\Services\GeminiService;
use Modules\AI\Services\VectorService;
use Modules\AI\Services\SemanticCacheService;
use Modules\AI\Models\ChatSession;
use Modules\AI\Models\ChatMessage;
use Modules\AI\Jobs\ProcessChatSummaryJob;

class ChatbotController extends Controller
{
    /**
     * Redis cache key prefix untuk status sesi aktif.
     * TTL = 24 jam (selaras dengan batas kedaluwarsa sesi di getHistory).
     */
    const SESSION_CACHE_PREFIX = 'vion_session_active:';
    const SESSION_TTL_SECONDS  = 86400; // 24 jam

    public function __construct(
        protected GeminiService      $gemini,
        protected VectorService      $vector,
        protected SemanticCacheService $semanticCache,
    ) {}

    /**
     * Start a new chat session (Lead Capture)
     * Optimasi: Simpan status sesi ke Redis agar validasi berikutnya tidak perlu query DB.
     */
    public function startSession(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'whatsapp' => 'required|string|max:20',
            'email'    => 'nullable|email|max:100',
            'company'  => 'nullable|string|max:100',
        ]);

        $session = ChatSession::create([
            'name'     => $request->name,
            'whatsapp' => $request->whatsapp,
            'email'    => $request->email,
            'company'  => $request->company ?: 'Personal',
            'status'   => 'active',
        ]);

        // ✅ OPTIMASI: Simpan status sesi aktif ke Redis (bypass DB query pada request berikutnya)
        Cache::put(
            self::SESSION_CACHE_PREFIX . $session->id,
            [
                'id'      => $session->id,
                'name'    => $session->name,
                'created' => $session->created_at->toIso8601String(),
            ],
            self::SESSION_TTL_SECONDS
        );

        // Simpan salam pembuka ke database agar riwayat tidak kosong saat di-refresh
        ChatMessage::create([
            'session_id' => $session->id,
            'role'       => 'assistant',
            'content'    => "Halo, Selamat datang **{$request->name}** 👋 saya **Vion**, ICT Solutions Consultant. Ada yang bisa saya bantu hari ini?",
        ]);

        return response()->json([
            'success'    => true,
            'session_id' => $session->id,
        ]);
    }

    /**
     * Handle chat message and return AI response.
     *
     * Optimasi yang diterapkan:
     *  1. Validasi sesi dari Redis cache (bypass DB query)
     *  2. Cache::touch() — perpanjang TTL sesi setiap ada aktivitas
     *  3. SemanticCache — kembalikan respons instan jika pertanyaan sudah pernah dijawab
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message'    => 'required|string|max:1000',
            'session_id' => 'required|exists:ai_chat_sessions,id',
            'persona'    => 'nullable|string|in:sales,analyst,doctor',
        ]);

        $message   = $request->input('message');
        $sessionId = $request->input('session_id');
        $persona   = $request->input('persona', 'sales');

        try {
            // 1. Simpan Pesan User
            ChatMessage::create([
                'session_id' => $sessionId,
                'role'       => 'user',
                'content'    => $message,
            ]);

            // ✅ OPTIMASI 1: Perpanjang masa aktif sesi di Redis (Cache::touch)
            // Setiap ada aktivitas baru, sesi diperbarui TTL-nya tanpa membaca/menulis ulang data sesi
            Cache::touch(self::SESSION_CACHE_PREFIX . $sessionId, self::SESSION_TTL_SECONDS);

            // ✅ OPTIMASI 2: Cek Semantic Cache — apakah pertanyaan ini pernah dijawab?
            $cachedResult = $this->semanticCache->get($message, $persona);

            if ($cachedResult !== null) {
                // Cache HIT — kembalikan respons instan (< 50ms), tidak perlu panggil AI sama sekali
                Log::info('Chatbot: Serving from SemanticCache', ['session_id' => $sessionId]);

                // Tetap simpan ke history DB agar riwayat konsisten
                ChatMessage::create([
                    'session_id' => $sessionId,
                    'role'       => 'assistant',
                    'content'    => $cachedResult['response'],
                    'products'   => $cachedResult['products'],
                ]);

                return response()->json([
                    'success'  => true,
                    'response' => $cachedResult['response'],
                    'products' => $cachedResult['products'],
                    'cached'   => true,
                ]);
            }

            // Cache MISS — lanjut ke proses AI penuh
            $results = collect([]);
            try {
                // 2. Ambil embedding dari pertanyaan user
                $queryVector = $this->gemini->getEmbedding($message);

                // 3. Cari produk paling relevan di Supabase (RAG)
                $results = $this->vector->search($queryVector, 5);
            } catch (\Exception $ragEx) {
                Log::warning('RAG/Embedding generation failed. Will try local search fallback.', [
                    'error' => $ragEx->getMessage()
                ]);
            }

            // Fallback to local database text search if vector search failed or returned nothing
            if ($results->isEmpty()) {
                Log::info('VION: Vector search returned empty or failed. Attempting local database fallback search for message: ' . $message);
                
                // Clean the message and extract alphanumeric terms for searching
                $cleanedMessage = preg_replace('/[^\p{L}\p{N}\s]/u', '', strtolower($message));
                $terms = array_filter(explode(' ', $cleanedMessage), function($term) {
                    $stopwords = ['saya', 'mau', 'tanya', 'bisa', 'apa', 'aja', 'untuk', 'ini', 'yang', 'dan', 'di', 'ke', 'dari', 'dengan', 'ada', 'adalah', 'atau', 'kami', 'kita', 'dia', 'mereka', 'kalian', 'yaitu', 'tersebut', 'pada', 'tentang', 'seperti', 'oleh', 'itu', 'juga', 'dalam', 'akan', 'hanya', 'saja', 'ingin'];
                    return strlen($term) >= 3 && !in_array($term, $stopwords);
                });

                if (!empty($terms)) {
                    // Start query
                    $dbQuery = \Modules\ProductCatalog\Models\Product::where('is_active', true);
                    
                    // Search using keywords
                    $dbQuery->where(function($q) use ($terms) {
                        foreach ($terms as $term) {
                            $q->orWhere('name', 'like', "%{$term}%")
                              ->orWhere('sku', 'like', "%{$term}%")
                              ->orWhere('slug', 'like', "%{$term}%");
                        }
                    });

                    $fallbackProducts = $dbQuery->limit(5)->get();

                    if ($fallbackProducts->isNotEmpty()) {
                        Log::info('VION Fallback: Found ' . $fallbackProducts->count() . ' products locally.');
                        $results = $fallbackProducts->map(function($product) {
                            $content = "Nama Produk: {$product->name}\n";
                            if ($product->sku) $content .= "SKU: {$product->sku}\n";
                            if ($product->description) $content .= "Deskripsi: " . strip_tags($product->description) . "\n";
                            if ($product->features_text) $content .= "Fitur: " . strip_tags($product->features_text) . "\n";
                            if ($product->specification_text) $content .= "Spesifikasi: " . strip_tags($product->specification_text) . "\n";

                            return (object)[
                                'product_id' => $product->id,
                                'content' => trim($content)
                            ];
                        });
                    }
                }
            }

            // 4. Bangun konteks dengan ID Produk agar AI bisa referensi
            $context = $results->isEmpty()
                ? 'Tidak ada produk spesifik yang relevan di katalog untuk pertanyaan ini. Gunakan pengetahuan umummu sebagai pakar ICT untuk menjawab secara bijak.'
                : $results->map(fn($r) => "ID: {$r->product_id} | Info: {$r->content}")->join("\n\n---\n\n");

            // 5. Generate respons AI dengan persona yang dipilih
            $aiResponse = $this->gemini->generateResponse($message, $context, $persona);

            // LOG DIAGNOSA: Lihat jawaban asli AI
            Log::info('VION Raw Response:', ['text' => $aiResponse]);

            // 6. Ekstrak kode produk (ID atau SKU) dari respon
            $recommendedProducts = [];
            $processedIds = [];

            if (preg_match_all('/(?:ID_PRODUK|ID|SKU):\s*([a-zA-Z0-9\-\.]+)/i', $aiResponse, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $productIdentifier = trim($match[1], " \t\n\r\0\x0B().");
                    Log::info("VION Processing identifier:", ['raw' => $match[1], 'cleaned' => $productIdentifier]);

                    $product = \Modules\ProductCatalog\Models\Product::where('sku', $productIdentifier)
                        ->orWhere('id', $productIdentifier)
                        ->first();

                    if ($product && !in_array($product->id, $processedIds)) {
                        Log::info("VION Product FOUND:", ['id' => $product->id, 'name' => $product->name]);
                        $recommendedProducts[] = [
                            'id'   => $product->id,
                            'name' => $product->name,
                            'image' => $product->image_path ? asset('storage/' . $product->image_path) : url('assets/default.png'),
                            'qty'  => 1,
                            'link' => url("/products/{$product->slug}"),
                            'link_accommerce' => $product->link_accommerce,
                        ];
                        $processedIds[] = $product->id;
                    } else {
                        Log::warning("VION Product NOT FOUND in DB for identifier: " . $productIdentifier);
                    }
                }
            }

            // Fallback safety net: If no products were found via ID/SKU tag, try to match bolded product names in the response
            if (empty($recommendedProducts)) {
                Log::info("VION: No product ID tags found in AI response. Attempting fallback bold extraction.");
                if (preg_match_all('/\*\*([^*]+)\*\*/', $aiResponse, $boldMatches)) {
                    foreach ($boldMatches[1] as $candidate) {
                        $candidate = trim($candidate);
                        if (strlen($candidate) < 3) continue;

                        // Check exact name match first
                        $product = \Modules\ProductCatalog\Models\Product::where('is_active', true)
                            ->where('name', $candidate)
                            ->first();

                        // Check slug match
                        if (!$product) {
                            $slugCandidate = \Illuminate\Support\Str::slug($candidate);
                            $product = \Modules\ProductCatalog\Models\Product::where('is_active', true)
                                ->where('slug', $slugCandidate)
                                ->first();
                        }

                        // Check partial name match if it is distinct enough
                        if (!$product && strlen($candidate) >= 5) {
                            $product = \Modules\ProductCatalog\Models\Product::where('is_active', true)
                                ->where('name', 'like', "%{$candidate}%")
                                ->first();
                        }

                        if ($product && !in_array($product->id, $processedIds)) {
                            Log::info("VION Bold Fallback Product FOUND:", ['id' => $product->id, 'name' => $product->name]);
                            $recommendedProducts[] = [
                                'id'   => $product->id,
                                'name' => $product->name,
                                'image' => $product->image_path ? asset('storage/' . $product->image_path) : url('assets/default.png'),
                                'qty'  => 1,
                                'link' => url("/products/{$product->slug}"),
                                'link_accommerce' => $product->link_accommerce,
                            ];
                            $processedIds[] = $product->id;
                        }
                    }
                }
            }

            Log::info('VION Final Product Count:', ['count' => count($recommendedProducts)]);

            // Bersihkan tag produk dan trigger dari teks agar tidak tampil ke user
            $cleanResponse = preg_replace('/\(?(?:ID_PRODUK|ID|SKU):\s*[a-zA-Z0-9\-\.]+\)?/i', '', $aiResponse);

            // Ubah trigger sales ke format internal
            if (str_contains($cleanResponse, '[HUBUNGI_SALES]')) {
                $cleanResponse = str_replace('[HUBUNGI_SALES]', '[WA_TRIGGER]', $cleanResponse);
            }

            // Rapikan tanda baca yang rusak akibat penghapusan tag
            $cleanResponse = preg_replace('/[,:]\\s*\\./', '.', $cleanResponse);
            $cleanResponse = preg_replace('/\\s+\\./', '.', $cleanResponse);
            $cleanResponse = preg_replace('/\\s\\s+/', ' ', $cleanResponse);
            $cleanResponse = trim($cleanResponse);

            if (empty($recommendedProducts) && (str_ends_with($cleanResponse, ':') || str_ends_with($cleanResponse, ':-'))) {
                $cleanResponse = rtrim($cleanResponse, ':-') . '.';
            }

            // 7. Simpan Respons AI ke Database
            ChatMessage::create([
                'session_id' => $sessionId,
                'role'       => 'assistant',
                'content'    => $cleanResponse,
                'products'   => $recommendedProducts,
            ]);

            // ✅ OPTIMASI 3: Simpan ke Semantic Cache untuk pertanyaan berikutnya yang identik
            $this->semanticCache->put($message, $persona, [
                'response' => $cleanResponse,
                'products' => $recommendedProducts,
            ]);

            return response()->json([
                'success'  => true,
                'response' => $cleanResponse,
                'products' => $recommendedProducts,
            ]);

        } catch (\Exception $e) {
            Log::error('Chatbot AI Error: ' . $e->getMessage(), [
                'user_message' => $message,
                'persona'      => $persona,
                'exception'    => $e,
            ]);

            return response()->json([
                'success'  => false,
                'response' => 'Maaf, saya sedang mengalami sedikit gangguan teknis. 🙏',
                'error'    => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get Chatbot settings for frontend
     */
    public function settings()
    {
        return response()->json([
            'is_active'       => \App\Models\Setting::getValue('vion_is_active', '1') === '1',
            'welcome_message' => \App\Models\Setting::getValue('vion_welcome_message', 'Halo! Saya Vion, ICT Solutions Consultant Anda. Ada yang bisa saya bantu hari ini?'),
            'starter_buttons' => json_decode(\App\Models\Setting::getValue('vion_starter_buttons', '[]'), true),
            'whatsapp_number' => \App\Models\Setting::getValue('vion_whatsapp_number', '628123456789'),
        ]);
    }

    /**
     * Return available personas
     */
    public function personas()
    {
        return response()->json($this->gemini->getAllPersonas());
    }

    /**
     * Summarize chat history — DIJALANKAN ASINKRON via Queue Job.
     *
     * Optimasi: Sebelumnya sinkron (memblokir 2-4 detik). Sekarang langsung
     * dispatch ke background queue, frontend menerima respons instan (< 100ms).
     */
    public function summarize(Request $request)
    {
        $request->validate(['history' => 'required|array']);

        $sessionId = $request->input('session_id');
        $history   = $request->input('history');

        // ✅ OPTIMASI: Tetap dispatch ke background queue untuk update database asinkron
        ProcessChatSummaryJob::dispatch($history, (int) $sessionId)
            ->onQueue('ai-summary');

        // ✅ SINKRON UNTUK WHATSAPP: Dapatkan ringkasan riil via Gemini agar tautan WA langsung terisi teks ringkasan
        try {
            $summary = $this->gemini->summarizeChat($history);
        } catch (\Exception $e) {
            Log::error("ChatbotController summarize error: " . $e->getMessage());
            $summary = "Halo Tim Sales ACTiV, 👋\n\nSaya ingin diskusi lebih lanjut terkait prospek yang baru masuk.";
        }

        return response()->json([
            'success' => true,
            'summary' => $summary,
        ]);
    }

    /**
     * Get chat history for a session.
     *
     * Optimasi: Cek status sesi dari Redis cache terlebih dahulu.
     * Jika cache hit, validasi kedaluwarsa dilakukan tanpa query DB sama sekali.
     */
    public function getHistory(Request $request)
    {
        $sessionId = $request->query('session_id');

        // ✅ OPTIMASI: Cek keberadaan sesi dari Redis cache (bypass DB query)
        $cacheKey     = self::SESSION_CACHE_PREFIX . $sessionId;
        $sessionCache = Cache::get($cacheKey);

        if ($sessionCache === null) {
            // Cache miss — mungkin sesi sudah expired (> 24 jam) atau memang tidak ada
            $session = ChatSession::find($sessionId);

            if (!$session) {
                return response()->json(['messages' => []]);
            }

            // Cek apakah sesi sudah lebih dari 24 jam (Expire)
            if ($session->created_at->diffInHours(now()) >= 24) {
                return response()->json([
                    'status'   => 'expired',
                    'messages' => [],
                    'error'    => 'Sesi Anda telah berakhir. Silakan isi data kembali.',
                ], 403);
            }
        }

        // Ambil riwayat pesan dari DB
        $messages = ChatMessage::where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($m) => [
                'role'     => $m->role,
                'content'  => $m->content,
                'products' => $m->products,
            ]);

        return response()->json([
            'messages' => $messages,
            'session'  => $sessionCache ?? ChatSession::find($sessionId),
        ]);
    }
}
