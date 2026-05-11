<?php

namespace Modules\AI\Services;

use Modules\AI\Drivers\GoogleAIDriver;
use Modules\AI\Drivers\VertexAIDriver;
use Modules\AI\Interfaces\GeminiDriverInterface;

class GeminiService
{
    /**
     * AI Squad Personas
     */
    protected array $personas = [
        'sales' => [
            'name'   => 'VION by ACTiV',
            'role'   => 'ICT Solutions Consultant',
            'prompt' => "Kamu adalah VION by ACTiV, ICT Solutions Consultant dari ACTiV. " .
                       "TUGAS: Rekomendasikan produk ICT berdasarkan katalog. " .
                       "FORMAT WAJIB: Setiap kali menyebutkan produk, tuliskan dengan format: 'NAMA_PRODUK (ID_PRODUK: {id}) : Penjelasan singkat.' " .
                       "CONTOH: 'Logitech Rally Plus (ID_PRODUK: 15) : Solusi kamera 4K untuk ruang meeting besar.' " .
                       "PENTING: Jangan mendeskripsikan produk tanpa menyebutkan Nama dan ID_PRODUK di depannya. " .
                       "MULTI-BRAND: Berikan opsi brand variatif (Logitech, Jabra, Yealink, dll). " .
                       "Gaya chat WA: Ringkas, profesional, to-the-point. " .
                       "CLOSING: Jika user tanya harga, tuliskan '[HUBUNGI_SALES]'.",
        ],



        'analyst' => [
            'name'   => 'Reza',
            'role'   => 'Technical Analyst ICT',
            'prompt' => 'Kamu adalah "Reza", seorang Technical Analyst ICT dari PT Alfa Cipta Teknologi Virtual (ACTiV). Tugasmu adalah menjelaskan spesifikasi teknis, kompatibilitas, dan cara kerja produk secara mendalam dan akurat. Jawab dalam Bahasa Indonesia yang teknis namun mudah dipahami. Gunakan poin-poin agar mudah dibaca. Jika ada spesifikasi di konteks, sebutkan secara detail.',
        ],

        'doctor' => [
            'name'   => 'Dewi',
            'role'   => 'Solution Architect',
            'prompt' => 'Kamu adalah "Dewi", seorang Solution Architect dari PT Alfa Cipta Teknologi Virtual (ACTiV). Tugasmu adalah mendiagnosa kebutuhan dan masalah customer, lalu merekomendasikan solusi teknologi ICT yang paling tepat seperti seorang dokter yang memberikan resep. Mulai dengan bertanya kebutuhan, lalu berikan rekomendasi solusi end-to-end yang komprehensif.',
        ],
    ];

    /**
     * The resolved AI driver instance.
     */
    protected GeminiDriverInterface $driver;

    public function __construct()
    {
        $this->driver = $this->resolveDriver();
    }

    /**
     * Resolve the correct AI driver based on the AI_PROVIDER env variable.
     */
    protected function resolveDriver(): GeminiDriverInterface
    {
        $provider = config('ai.provider', 'google');

        return match ($provider) {
            'vertex' => new VertexAIDriver($this->personas),
            default  => new GoogleAIDriver($this->personas),
        };
    }

    // -------------------------------------------------------------------------
    // Proxy methods — delegates all calls to the active driver
    // -------------------------------------------------------------------------

    /**
     * Get vector embedding for a given text.
     */
    public function getEmbedding(string $text): array
    {
        return $this->driver->getEmbedding($text);
    }

    /**
     * Generate a contextual response based on persona and retrieved context.
     */
    public function generateResponse(string $userMessage, string $context, string $persona = 'sales'): string
    {
        return $this->driver->generateResponse($userMessage, $context, $persona);
    }

    /**
     * Generate a concise summary of the chat for WhatsApp leads.
     */
    public function summarizeChat(array $history): string
    {
        return $this->driver->summarizeChat($history);
    }

    /**
     * Force-refresh the AI context cache.
     * Useful when product catalog is updated.
     */
    public function refreshCache(string $persona = 'sales'): void
    {
        if (method_exists($this->driver, 'refreshCache')) {
            $this->driver->refreshCache($persona);
        }
    }

    // -------------------------------------------------------------------------
    // Persona helpers (provider-agnostic)
    // -------------------------------------------------------------------------

    public function getPersona(string $persona = 'sales'): array
    {
        return $this->personas[$persona] ?? $this->personas['sales'];
    }

    public function getAllPersonas(): array
    {
        return $this->personas;
    }

    /**
     * Returns which AI provider is currently active.
     */
    public function activeProvider(): string
    {
        return config('ai.provider', 'google');
    }
}
