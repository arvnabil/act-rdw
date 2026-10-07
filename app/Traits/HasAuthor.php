<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HasAuthor
{
    public static function bootHasAuthor()
    {
        static::creating(function ($model) {
            if (Auth::check() && empty($model->user_id)) {
                $model->user_id = Auth::id();
            }
        });
    }

    public function author()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
