<?php

namespace Modules\ProductCatalog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImageCleanup;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'name',
    'slug',
    'icon',
    'sort_order',
    'is_active',
])]
class ProductCategory extends Model
{
    use HasFactory, HasImageCleanup;

    protected $cleanupFields = ['icon'];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_category_product');
    }
}
