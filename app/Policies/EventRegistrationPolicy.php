<?php
namespace App\Policies;
use App\Models\User;
use Modules\Events\Models\EventRegistration;
use Illuminate\Auth\Access\HandlesAuthorization;
class EventRegistrationPolicy
{
    use HandlesAuthorization;
    public function viewAny(User \): bool { return \->hasAnyPermission(['view_any_event', 'view_event', 'create_event', 'update_event', 'delete_event']); }
    public function view(User \, EventRegistration \): bool { return \->hasPermissionTo('view_event'); }
    public function create(User \): bool { return \->hasPermissionTo('create_event'); }
    public function update(User \, EventRegistration \): bool { return \->hasPermissionTo('update_event'); }
    public function delete(User \, EventRegistration \): bool { return \->hasPermissionTo('delete_event'); }
    public function deleteAny(User \): bool { return \->hasPermissionTo('delete_any_event'); }
    public function restore(User \, EventRegistration \): bool { return \->hasPermissionTo('delete_event'); }
    public function forceDelete(User \, EventRegistration \): bool { return \->hasPermissionTo('delete_any_event'); }
}
