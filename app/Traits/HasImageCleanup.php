<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

trait HasImageCleanup
{
    protected static function bootHasImageCleanup(): void
    {
        static::updated(function (Model $model) {
            foreach ($model->getCleanupFields() as $field) {
                if ($model->isDirty($field)) {
                    $original = $model->getOriginal($field);
                    if ($original) {
                        Storage::disk('public')->delete($original);
                    }
                }
            }
        });

        static::deleted(function (Model $model) {
            foreach ($model->getCleanupFields() as $field) {
                $value = $model->$field;
                if ($value) {
                    Storage::disk('public')->delete($value);
                }
            }
        });
    }

    public function getCleanupFields(): array
    {
        return property_exists($this, 'cleanupFields') ? $this->cleanupFields : [];
    }
}
