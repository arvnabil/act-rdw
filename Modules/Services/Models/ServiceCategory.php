<?php

namespace Modules\Services\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['service_id', 'label', 'value'])]
class ServiceCategory extends Model
{

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function solutions(): BelongsToMany
    {
        return $this->belongsToMany(ServiceSolution::class, 'service_solution_category');
    }
}
