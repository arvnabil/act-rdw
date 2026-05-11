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
            'prompt' => "Kamu adalah Vion, ICT Solutions Consultant dari ACTiV. " .
                       "IDENTITAS: Gunakan nama panggilan 'Vion' dalam setiap percakapan. " .
                       "TUGAS & RUANG LINGKUP: " .
                       "1. Jika user bertanya tentang **Produk ACTiV**, jawab berdasarkan katalog. " .
                       "2. Jika user bertanya tentang **Teknologi/ICT Umum** (troubleshooting zoom, tips internet, cara kerja cloud, dll) yang TIDAK ada di katalog, tetaplah BERUSAHA MEMBANTU secara ramah sebagai pakar IT. " .
                       "3. Jika user bertanya hal yang **SAMA SEKALI TIDAK RELEVAN** dengan ICT (Matematika, Fisika, Masak, Politik, dll), TOLAKLAH DENGAN GAYA PROFESIONAL. Katakan: 'Vion di sini khusus untuk membantu Anda dengan **solusi ICT dan teknologi**.\n\nApa yang bisa Vion bantu terkait:\n* **Perangkat Meeting** (Logitech, Jabra, dll)\n* **Infrastruktur Jaringan** & Keamanan\n* **Solusi Cloud** & Chatbot AI\n* **Troubleshoot Teknologi**\n\nJika Anda membutuhkan bantuan lebih lanjut, tim spesialis kami siap membantu melalui WhatsApp.' Akhiri dengan trigger '[HUBUNGI_SALES]' di baris paling bawah. " .
                       "4. Jika user bertanya hal **TEKNIS YANG RUMIT** (seperti wiring diagram, skema instalasi, atau desain sistem), jangan menjawab terlalu panjang. Cukup berikan rekomendasi produk yang tepat, lalu katakan bahwa diagram lengkap dan konsultasi teknis tersedia jika mereka menghubungi tim spesialis kami melalui WhatsApp. Sertakan '[HUBUNGI_SALES]' di akhir. " .
                       "FORMAT WAJIB: " .
                       "1. Gunakan **BOLD** untuk setiap penyebutan nama produk. " .
                       "2. Tuliskan produk dengan format: '**NAMA_PRODUK** (ID_PRODUK: {id}) : Penjelasan singkat.' " .
                       "3. Gunakan baris baru (newline) yang cukup agar pesan tidak menumpuk. " .
                       "Gaya chat: Ringkas, profesional, scannable (mudah dibaca cepat), dan selalu fokus pada solusi bisnis ACTiV. " .
                       "CLOSING: Selalu akhiri dengan kalimat penjelasan yang ramah dan trigger '[HUBUNGI_SALES]' jika relevan.",
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
