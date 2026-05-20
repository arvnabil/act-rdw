<?php

namespace Modules\Services\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\ProductCatalog\Models\Brand;

use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'service_id',
    'title',
    'slug',
    'subtitle',
    'description',
    'thumbnail',
    'breadcrumb_image',
    'show_breadcrumb',
    'features',
    'showcase',
    'wa_message',
    'configurator_slug',
    'show_showcase',
    'sort_order'
])]
class ServiceSolution extends Model
{
    use \Modules\SEO\Traits\HasSeoMeta;
    use \App\Traits\HasImageCleanup;

    protected $cleanupFields = ['thumbnail', 'breadcrumb_image', 'features', 'showcase'];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'features' => 'array',
            'showcase' => 'array',
            'show_showcase' => 'boolean',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ServiceCategory::class, 'service_solution_category');
    }

    public function brands(): BelongsToMany
    {
        return $this->belongsToMany(Brand::class, 'service_solution_brand');
    }

    public function configuratorOptions(): HasMany
    {
        return $this->hasMany(ConfiguratorOption::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(\Modules\ProductCatalog\Models\Product::class, 'product_service_solution');
    }
}
