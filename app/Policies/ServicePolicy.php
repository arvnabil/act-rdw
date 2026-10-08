<?php

namespace App\Policies;

use App\Models\User;
use Modules\Services\Models\Service;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServicePolicy
{
    use HandlesAuthorization;

    public function viewAny(User ): bool
    {
        return ->hasAnyPermission(['view_any_service', 'view_service', 'create_service', 'update_service', 'delete_service', 'delete_any_service']);
    }

    public function view(User , Service ): bool
    {
        return ->hasPermissionTo('view_service');
    }

    public function create(User ): bool
    {
        return ->hasPermissionTo('create_service');
    }

    public function update(User , Service ): bool
    {
        return ->hasPermissionTo('update_service');
    }

    public function delete(User , Service ): bool
    {
        return ->hasPermissionTo('delete_service');
    }

    public function deleteAny(User ): bool
    {
        return ->hasPermissionTo('delete_any_service');
    }

    public function restore(User , Service ): bool
    {
        return ->hasPermissionTo('delete_service');
    }

    public function forceDelete(User , Service ): bool
    {
        return ->hasPermissionTo('delete_any_service');
    }
}
