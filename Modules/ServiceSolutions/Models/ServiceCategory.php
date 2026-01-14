<?php

namespace Modules\ServiceSolutions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ServiceCategory extends Model
{
    protected $fillable = ['service_id', 'label', 'value'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function solutions(): BelongsToMany
    {
        return $this->belongsToMany(ServiceSolution::class, 'service_solution_category');
    }
}
