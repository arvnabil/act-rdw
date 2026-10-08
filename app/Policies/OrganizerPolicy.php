<?php
namespace App\Policies;
use App\Models\User;
use Modules\Events\Models\Organizer;
use Illuminate\Auth\Access\HandlesAuthorization;
class OrganizerPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user): bool { return $user->hasAnyPermission(['view_any_event', 'view_event', 'create_event', 'update_event', 'delete_event']); }
    public function view(User $user, Organizer $r): bool { return $user->hasPermissionTo('view_event'); }
    public function create(User $user): bool { return $user->hasPermissionTo('create_event'); }
    public function update(User $user, Organizer $r): bool { return $user->hasPermissionTo('update_event'); }
    public function delete(User $user, Organizer $r): bool { return $user->hasPermissionTo('delete_event'); }
    public function deleteAny(User $user): bool { return $user->hasPermissionTo('delete_event'); }
    public function restore(User $user, Organizer $r): bool { return $user->hasPermissionTo('delete_event'); }
    public function forceDelete(User $user, Organizer $r): bool { return $user->hasPermissionTo('delete_event'); }
}
