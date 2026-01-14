<?php

namespace Modules\ServiceSolutions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ServiceSolutions\Models\ConfiguratorStep;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Configurator extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
    ];

    public function steps(): HasMany
    {
        return $this->hasMany(ConfiguratorStep::class)->orderBy('sort_order');
    }
}
