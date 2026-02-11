<?php

namespace Modules\ServiceSolutions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\ServiceSolutions\Models\ConfiguratorQuestion;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\HasImageCleanup;

class ConfiguratorStep extends Model
{
    use HasFactory, HasImageCleanup;

    protected $cleanupFields = ['image'];

    protected $fillable = [
        'configurator_id',
        'name',
        'title',
        'description',
        'sort_order',
        'layout',
        'image',
        'conditions',
    ];

    protected $casts = [
        'conditions' => 'array',
    ];

    public function configurator(): BelongsTo
    {
        return $this->belongsTo(Configurator::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ConfiguratorQuestion::class, 'step_id')->orderBy('sort_order');
    }
}
