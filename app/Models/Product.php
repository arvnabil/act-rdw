<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id', 'brand_id', 'name', 'slug',
        'description', 'image_path', 'sku', 'solution_type',
        'datasheet_url', 'tags', 'specs', 'specification_text',
        'features', 'features_text', 'is_active'
    ];

    protected $casts = [
        'specs' => 'array',
        'tags' => 'array',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    // A Product matches multiple criteria (e.g. "Small Room", "Budget Friendly")
    public function criteria()
    {
        return $this->belongsToMany(ConfiguratorCriteria::class, 'product_criteria', 'product_id', 'configurator_criteria_id');
    }
}
