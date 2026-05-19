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
        try {
            $url = config('services.supabase.url') . '/rest/v1/product_embeddings';
            $key = config('services.supabase.key');

            if (empty($url) || empty($key)) {
                \Log::warning('Supabase URL or Key not configured.');
                return false;
            }

            $response = Http::timeout(5)->withHeaders([
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
        } catch (\Exception $e) {
            \Log::warning('VectorService Upsert Error (Supabase Connection Failed): ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Perform semantic search using RPC call via REST API
     */
    public function search(array $queryVector, int $limit = 3)
    {
        try {
            $url = config('services.supabase.url') . '/rest/v1/rpc/match_products';
            $key = config('services.supabase.key');

            if (empty($url) || empty($key)) {
                \Log::warning('Supabase URL or Key not configured.');
                return collect([]);
            }

            $response = Http::timeout(5)->withHeaders([
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
        } catch (\Exception $e) {
            \Log::warning('VectorService Search Error (Supabase Connection Failed): ' . $e->getMessage());
        }

        return collect([]);
    }
}
