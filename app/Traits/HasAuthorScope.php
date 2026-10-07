<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * HasAuthorScope - Trait untuk Filament Resource
 * 
 * Administrator : bisa lihat SEMUA data
 * Co-Admin/Editor : hanya lihat data milik sendiri (user_id = auth()->id())
 * Viewer : hanya bisa lihat data (read-only)
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

        // Administrator bisa lihat semua data
        if ($user->hasRole('administrator')) {
            return $query;
        }

        // Co-Admin & Editor hanya lihat data sendiri
        if ($user->hasAnyRole(['co-admin', 'editor'])) {
            return $query->where('user_id', $user->id);
        }

        // Viewer hanya bisa lihat semua tapi read-only (enforced via permissions)
        return $query;
    }
}
