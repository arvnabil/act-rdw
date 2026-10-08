<?php

namespace App\Policies;

use App\Models\User;
use Modules\Services\Models\Configurator;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConfiguratorPolicy
{
    use HandlesAuthorization;

    public function viewAny(User ): bool
    {
        return ->hasAnyPermission(['view_any_service', 'view_service', 'create_service', 'update_service', 'delete_service']);
    }

    public function view(User , Configurator ): bool
    {
        return ->hasPermissionTo('view_service');
    }

    public function create(User ): bool
    {
        return ->hasPermissionTo('create_service');
    }

    public function update(User , Configurator ): bool
    {
        return ->hasPermissionTo('update_service');
    }

    public function delete(User , Configurator ): bool
    {
        return ->hasPermissionTo('delete_service');
    }

    public function deleteAny(User ): bool
    {
        return ->hasPermissionTo('delete_any_service');
    }

    public function restore(User , Configurator ): bool
    {
        return ->hasPermissionTo('delete_service');
    }

    public function forceDelete(User , Configurator ): bool
    {
        return ->hasPermissionTo('delete_any_service');
    }
}
