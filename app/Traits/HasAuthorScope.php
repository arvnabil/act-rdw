<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * HasAuthorScope - Trait untuk Filament Resource
 * 
 * Administrator : bisa lihat SEMUA data
 * Co-Admin/Editor : bisa lihat SEMUA data
 * Viewer : bisa lihat SEMUA data (read-only)
 */
trait HasAuthorScope
{
    /**
     * Apply query scope berdasarkan role user.
     * Tambahkan di Resource: getEloquentQuery()
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = Auth::user();

        if (!$user) {
            return $query->whereRaw('1 = 0'); // return empty
        }

        // Semua role bisa melihat semua data agar kolaborasi lebih mudah
        return $query;
    }
}
