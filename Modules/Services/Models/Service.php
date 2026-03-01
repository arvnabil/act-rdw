<?php

namespace Modules\Services\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ProductCatalog\Models\Product;
use Modules\SEO\Traits\HasSeoMeta;


use App\Traits\HasImageCleanup;

class Service extends Model
{
    use HasFactory, HasSeoMeta, HasImageCleanup;

    protected $cleanupFields = ['featured_image', 'breadcrumb_image', 'thumbnail', 'icon'];
    protected $richEditorCleanupFields = ['content'];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'content',
        'excerpt',
        'featured_image',
        'thumbnail',
        'icon',
        'breadcrumb_image',
        'show_breadcrumb',
        'hero_subtitle',
        'grid_title',
        'sort_order'
    ];

    // A service has many products (e.g. Surveillance sets)
    public function products()
    {
        return $this->hasMany(Product::class);
    }



    public function categories()
    {
        return $this->hasMany(ServiceCategory::class);
    }

    public function solutions()
    {
        return $this->hasMany(ServiceSolution::class);
    }
}
