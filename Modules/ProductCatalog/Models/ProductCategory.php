<?php

namespace Modules\ProductCatalog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasImageCleanup;

class ProductCategory extends Model
{
    use HasFactory, HasImageCleanup;

    protected $cleanupFields = ['icon'];

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_category_product');
    }
}
