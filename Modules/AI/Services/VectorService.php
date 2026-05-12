<?php

namespace Modules\AI\Services;

use Modules\AI\Models\ProductEmbedding;
use Illuminate\Support\Facades\Http;

class VectorService
{
    /**
     * Store or update product embedding via REST API
     */
    public function upsert(int $productId, array $embedding, string $content, array $metadata = [])
    {
        $url = config('services.supabase.url') . '/rest/v1/product_embeddings';
        $key = config('services.supabase.key');

        $response = Http::withHeaders([
            'apikey' => $key,
            'Authorization' => 'Bearer ' . $key,
            'Content-Type' => 'application/json',
            'Prefer' => 'resolution=merge-duplicates'
        ])->post($url, [
            'product_id' => $productId,
            'embedding' => '[' . implode(',', $embedding) . ']',
            'content' => $content,
            'metadata' => $metadata,
            'updated_at' => now()->toIso8601String(),
        ]);

        return $response->successful();
    }

    /**
     * Perform semantic search using RPC call via REST API
     */
    public function search(array $queryVector, int $limit = 3)
    {
        $url = config('services.supabase.url') . '/rest/v1/rpc/match_products';
        $key = config('services.supabase.key');

        $response = Http::withHeaders([
            'apikey' => $key,
            'Authorization' => 'Bearer ' . $key,
            'Content-Type' => 'application/json',
        ])->post($url, [
            'query_embedding' => '[' . implode(',', $queryVector) . ']',
            'match_threshold' => 0.3,
            'match_count' => $limit,
        ]);

        if ($response->successful()) {
            // Convert array back to objects to maintain compatibility with existing code
            return collect($response->json())->map(function($item) {
                return (object)$item;
            });
        }

        \Log::error('Supabase RPC Search Error: ' . $response->body());
        return collect([]);
    }
}
