<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguratorCriteria extends Model
{
    use HasFactory;

    protected $table = 'configurator_criteria';

    protected $fillable = ['service_id', 'type', 'value', 'label', 'icon_path'];

    // Criteria belongs to a Service (e.g. "Small Room" belongs to Room Configurator)
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // Criteria can belong to many products (filtering mechanism)
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_criteria', 'configurator_criteria_id', 'product_id');
    }
}
