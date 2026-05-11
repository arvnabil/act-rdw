<?php

namespace Modules\AI\Models;

use Illuminate\Database\Eloquent\Model;
use Pgvector\Laravel\Vector;

class ProductEmbedding extends Model
{
    protected $connection = 'pgsql_vector';
    protected $table = 'product_embeddings';
    
    protected $fillable = [
        'product_id',
        'embedding',
        'content',
        'metadata',
    ];

    protected $casts = [
        'embedding' => Vector::class,
        'metadata' => 'json',
    ];
}
