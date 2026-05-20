<?php

namespace Modules\Services\Models;

use Modules\ProductCatalog\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\HasImageCleanup;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'question_id',
    'label',
    'value',
    'metadata',
    'sort_order',
    'conditions',
])]
class ConfiguratorOption extends Model
{
    use HasFactory, HasImageCleanup;

    protected $cleanupFields = ['metadata'];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'conditions' => 'array',
        ];
    }

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
