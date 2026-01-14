<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ServiceSolutions\Models\Service;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id', 'brand_id', 'name', 'slug',
        'description', 'image_path', 'sku', 'solution_type',
        'datasheet_url', 'tags', 'specs', 'specification_text',
        'features', 'features_text', 'is_active',
        'price', 'link_accommerce'
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



    public function configuratorOptions()
    {
        return $this->belongsToMany(\Modules\ServiceSolutions\Models\ConfiguratorOption::class, 'product_configurator_option');
    }
}
