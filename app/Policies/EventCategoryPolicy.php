<?php
namespace App\Policies;
use App\Models\User;
use Modules\Events\Models\EventCategory;
use Illuminate\Auth\Access\HandlesAuthorization;
class EventCategoryPolicy
{
    use HandlesAuthorization;
    public function viewAny(User \): bool { return \->hasAnyPermission(['view_any_event', 'view_event', 'create_event', 'update_event', 'delete_event']); }
    public function view(User \, EventCategory \): bool { return \->hasPermissionTo('view_event'); }
    public function create(User \): bool { return \->hasPermissionTo('create_event'); }
    public function update(User \, EventCategory \): bool { return \->hasPermissionTo('update_event'); }
    public function delete(User \, EventCategory \): bool { return \->hasPermissionTo('delete_event'); }
    public function deleteAny(User \): bool { return \->hasPermissionTo('delete_any_event'); }
    public function restore(User \, EventCategory \): bool { return \->hasPermissionTo('delete_event'); }
    public function forceDelete(User \, EventCategory \): bool { return \->hasPermissionTo('delete_any_event'); }
}
