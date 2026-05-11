<?php

namespace Modules\AI\Services;

use Modules\AI\Models\ProductEmbedding;
use Illuminate\Support\Facades\DB;

class VectorService
{
    /**
     * Store or update product embedding
     */
    public function upsert(int $productId, array $embedding, string $content, array $metadata = [])
    {
        return DB::connection('pgsql_vector')->table('product_embeddings')->updateOrInsert(
            ['product_id' => $productId],
            [
                'embedding' => '[' . implode(',', $embedding) . ']',
                'content' => $content,
                'metadata' => json_encode($metadata),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Perform semantic search using Cosine Similarity
     */
    public function search(array $queryVector, int $limit = 3)
    {
        $vectorString = '[' . implode(',', $queryVector) . ']';
        
        return DB::connection('pgsql_vector')->table('product_embeddings')
            ->orderByRaw('embedding <=> ?', [$vectorString])
            ->limit($limit)
            ->get();
    }

}
