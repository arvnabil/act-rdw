<?php

namespace Modules\ServiceSolutions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\ServiceSolutions\Models\ConfiguratorOption;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConfiguratorQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'step_id',
        'label',
        'variable_name',
        'type',
        'is_mandatory',
        'sort_order',
        'conditions',
    ];

    protected $casts = [
        'conditions' => 'array',
        'is_mandatory' => 'boolean',
    ];

    public function step(): BelongsTo
    {
        return $this->belongsTo(ConfiguratorStep::class, 'step_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(ConfiguratorOption::class, 'question_id')->orderBy('sort_order');
    }
}
