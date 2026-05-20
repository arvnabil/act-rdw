<?php

namespace Modules\AI\Models;

use Illuminate\Database\Eloquent\Model;
use Pgvector\Laravel\Vector;
use Illuminate\Database\Eloquent\Attributes\Connection;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Connection('pgsql_vector')]
#[Table('product_embeddings')]
#[Fillable(['product_id', 'embedding', 'content', 'metadata'])]
class ProductEmbedding extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'embedding' => Vector::class,
            'metadata' => 'json',
        ];
    }
}
