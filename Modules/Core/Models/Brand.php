<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasImageCleanup;

class Brand extends Model
{
    use HasFactory, HasImageCleanup;

    protected $cleanupFields = ['image', 'breadcrumb_image', 'thumbnail', 'logo_path', 'landing_config'];

    protected $fillable = ['name', 'slug', 'logo_path', 'website_url', 'image', 'breadcrumb_image', 'thumbnail', 'desc', 'category', 'is_featured', 'show_breadcrumb', 'landing_config'];


    protected $casts = [
        'landing_config' => 'array',
        'is_featured' => 'boolean',
        'show_breadcrumb' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function serviceSolutions()
    {
        return $this->belongsToMany(\Modules\ServiceSolutions\Models\ServiceSolution::class, 'service_solution_brand');
    }

    public function seo()
    {
        return $this->morphOne(\App\Models\SeoMeta::class, 'seoable');
    }

    public function getLandingConfigAttribute($value)
    {
        $decoded = is_string($value) ? json_decode($value, true) : $value;
        if (!is_array($decoded)) {
            return $decoded ?: [];
        }

        // Auto-generate rel for cta_url
        if (isset($decoded['cta_url']) && is_string($decoded['cta_url'])) {
            $decoded['cta_url_rel'] = \App\Helpers\SeoHelper::get_rel($decoded['cta_url']);
        }

        return $decoded;
    }
}
