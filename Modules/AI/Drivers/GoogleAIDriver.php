<?php

namespace Modules\AI\Drivers;

use Gemini\Laravel\Facades\Gemini;
use Modules\AI\Interfaces\GeminiDriverInterface;

class GoogleAIDriver implements GeminiDriverInterface
{
    protected array $personas;

    public function __construct(array $personas)
    {
        $this->personas = $personas;
    }

    /**
     * Get a vector embedding for a given text using Google AI Studio.
     */
    public function getEmbedding(string $text): array
    {
        $response = Gemini::embeddingModel('models/gemini-embedding-001')->embedContent($text);
        return $response->embedding->values;
    }

    /**
     * Generate a contextual response based on persona and retrieved context.
     */
    public function generateResponse(string $userMessage, string $context, string $persona = 'sales'): string
    {
        $personaData = $this->personas[$persona] ?? $this->personas['sales'];

        $systemInstruction = $personaData['prompt'];

        $fullPrompt = "DATA KONTEKS PRODUK DARI KATALOG (Gunakan jika relevan):\n{$context}\n\n" .
                      "PERTANYAAN USER: {$userMessage}";

        $result = Gemini::generativeModel('models/gemini-2.0-flash')
            ->withConfig(\Gemini\Data\GenerationConfig::parse([
                'maxOutputTokens' => 1000,
                'temperature' => 0.7,
            ]))
            ->withSystemInstruction(\Gemini\Data\Content::parse($systemInstruction))
            ->startChat()
            ->sendMessage($fullPrompt);

        return $result->text();
    }

    /**
     * Summarize a chat history array into a short summary.
     */
    public function summarizeChat(array $history): string
    {
        $chatLog = "";
        foreach ($history as $msg) {
            $role = ($msg['role'] === 'user') ? 'Customer' : 'VION by ACTiV';
            $content = is_array($msg['content']) ? json_encode($msg['content']) : $msg['content'];
            $chatLog .= "{$role}: {$content}\n";
        }

        $prompt = "Berdasarkan log percakapan berikut, buatkan rangkuman SANGAT SINGKAT (maksimal 20 kata) untuk dikirim ke tim sales. " .
                  "Fokus pada: Produk apa yang dicari dan berapa jumlahnya/kapasitasnya.\n\n" .
                  "LOG PERCAKAPAN:\n{$chatLog}\n\n" .
                  "RANGKUMAN (Bahasa Indonesia):";

        try {
            $result = Gemini::generativeModel('models/gemini-2.0-flash')
                ->startChat()
                ->sendMessage($prompt);
            return trim($result->text());
        } catch (\Exception $e) {
            return "Tertarik diskusi lebih lanjut.";
        }
    }
}
