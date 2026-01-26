<?php

namespace Modules\ServiceSolutions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\Brand;

class ServiceSolution extends Model
{
    use \App\Traits\HasSeoMeta;

    protected $fillable = [
        'service_id',
        'title',
        'slug',
        'subtitle',
        'description',
        'thumbnail',
        'features',
        'showcase',
        'wa_message',
        'configurator_slug',
        'sort_order'
    ];

    protected $casts = [
        'features' => 'array',
        'showcase' => 'array',
    ];

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
        return $this->belongsToMany(\Modules\Core\Models\Product::class, 'product_service_solution');
    }
}
