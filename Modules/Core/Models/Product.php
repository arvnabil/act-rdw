<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ServiceSolutions\Models\Service;
use App\Traits\HasSeoMeta;

use App\Traits\HasImageCleanup;

class Product extends Model
{
    use HasFactory, HasSeoMeta, HasImageCleanup;

    protected $cleanupFields = ['image_path', 'datasheet_url'];
    protected $richEditorCleanupFields = ['description', 'specification_text', 'features_text'];

    protected $fillable = [
        'service_id', 'product_category_id', 'brand_id', 'name', 'slug',
        'description', 'image_path', 'sku', 'solution_type',
        'datasheet_url', 'tags', 'specs', 'specification_text',
        'features', 'features_text', 'is_active', 'is_featured',
        'features', 'features_text', 'is_active', 'is_featured',
        'price', 'link_accommerce', 'whatsapp_note'
    ];

    protected $casts = [
        'specs' => 'array',
        'tags' => 'array',
        'features' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
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

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function solutions()
    {
        return $this->belongsToMany(\Modules\ServiceSolutions\Models\ServiceSolution::class, 'product_service_solution');
    }
}
