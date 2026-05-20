<?php

namespace Modules\Services\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Services\Models\ConfiguratorStep;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\HasImageCleanup;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'name',
    'slug',
    'description',
    'image',
    'is_active',
])]
class Configurator extends Model
{
    use HasFactory, HasImageCleanup;

    protected $cleanupFields = ['image'];

    public function steps(): HasMany
    {
        return $this->hasMany(ConfiguratorStep::class)->orderBy('sort_order');
    }
}
