<?php

namespace App\Policies;

use App\Models\User;
use Modules\Events\Models\Event;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    public function viewAny(User \): bool
    {
        return \->hasAnyPermission(['view_any_event', 'view_event', 'create_event', 'update_event', 'delete_event', 'delete_any_event']);
    }

    public function view(User \, Event \): bool
    {
        return \->hasPermissionTo('view_event');
    }

    public function create(User \): bool
    {
        return \->hasPermissionTo('create_event');
    }

    public function update(User \, Event \): bool
    {
        return \->hasPermissionTo('update_event');
    }

    public function delete(User \, Event \): bool
    {
        return \->hasPermissionTo('delete_event');
    }

    public function deleteAny(User \): bool
    {
        return \->hasPermissionTo('delete_any_event');
    }

    public function restore(User \, Event \): bool
    {
        return \->hasPermissionTo('delete_event');
    }

    public function forceDelete(User \, Event \): bool
    {
        return \->hasPermissionTo('delete_any_event');
    }
}
