<?php

namespace Modules\AI\Interfaces;

interface GeminiDriverInterface
{
    /**
     * Generate a contextual AI response based on persona and context.
     */
    public function generateResponse(string $userMessage, string $context, string $persona): string;

    /**
     * Get a vector embedding for a given text.
     */
    public function getEmbedding(string $text): array;

    /**
     * Summarize a chat history array into a short summary.
     */
    public function summarizeChat(array $history): string;
}
