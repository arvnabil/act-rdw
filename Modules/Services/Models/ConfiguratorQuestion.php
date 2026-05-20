<?php

namespace Modules\Services\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Services\Models\ConfiguratorOption;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'step_id',
    'label',
    'variable_name',
    'type',
    'is_mandatory',
    'sort_order',
    'conditions',
])]
class ConfiguratorQuestion extends Model
{
    use HasFactory;


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'conditions' => 'array',
            'is_mandatory' => 'boolean',
        ];
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(ConfiguratorStep::class, 'step_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(ConfiguratorOption::class, 'question_id')->orderBy('sort_order');
    }
}
