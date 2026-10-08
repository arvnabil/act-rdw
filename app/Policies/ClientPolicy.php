<?php

namespace App\Policies;

use App\Models\User;
use Modules\Clients\Models\Client;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
{
    use HandlesAuthorization;

    public function viewAny(User \): bool
    {
        return \->hasAnyPermission(['view_any_client', 'view_client', 'create_client', 'update_client', 'delete_client', 'delete_any_client']);
    }

    public function view(User \, Client \): bool
    {
        return \->hasPermissionTo('view_client');
    }

    public function create(User \): bool
    {
        return \->hasPermissionTo('create_client');
    }

    public function update(User \, Client \): bool
    {
        return \->hasPermissionTo('update_client');
    }

    public function delete(User \, Client \): bool
    {
        return \->hasPermissionTo('delete_client');
    }

    public function deleteAny(User \): bool
    {
        return \->hasPermissionTo('delete_any_client');
    }

    public function restore(User \, Client \): bool
    {
        return \->hasPermissionTo('delete_client');
    }

    public function forceDelete(User \, Client \): bool
    {
        return \->hasPermissionTo('delete_any_client');
    }
}
