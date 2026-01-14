<?php

namespace Modules\ServiceSolutions\Models;

use Modules\Core\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConfiguratorOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'label',
        'value',
        'metadata',
        'sort_order',
        'conditions',
    ];

    protected $casts = [
        'metadata' => 'array',
        'conditions' => 'array',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(ConfiguratorQuestion::class);
    }

    public function serviceSolution(): BelongsTo
    {
        return $this->belongsTo(ServiceSolution::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_configurator_option');
    }
}
