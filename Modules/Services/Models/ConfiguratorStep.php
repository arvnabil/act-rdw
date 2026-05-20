<?php

namespace Modules\Services\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Services\Models\ConfiguratorQuestion;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\HasImageCleanup;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'configurator_id',
    'name',
    'title',
    'description',
    'sort_order',
    'layout',
    'image',
    'conditions',
])]
class ConfiguratorStep extends Model
{
    use HasFactory, HasImageCleanup;

    protected $cleanupFields = ['image'];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'conditions' => 'array',
        ];
    }

    public function configurator(): BelongsTo
    {
        return $this->belongsTo(Configurator::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ConfiguratorQuestion::class, 'step_id')->orderBy('sort_order');
    }
}
