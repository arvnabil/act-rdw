<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'logo_path', 'website_url', 'image', 'desc', 'category', 'is_featured', 'landing_config'];

    protected $casts = [
        'landing_config' => 'array',
        'is_featured' => 'boolean',
        'category' => 'array',
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
}
