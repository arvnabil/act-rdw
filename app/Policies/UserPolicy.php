<?php
namespace App\Policies;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
class UserPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user): bool { return $user->hasAnyPermission(['view_any_user', 'view_user', 'create_user', 'update_user', 'delete_user', 'delete_any_user']); }
    public function view(User $user, User $target): bool { return $user->hasPermissionTo('view_user'); }
    public function create(User $user): bool { return $user->hasPermissionTo('create_user'); }
    public function update(User $user, User $target): bool { return $user->hasPermissionTo('update_user'); }
    public function delete(User $user, User $target): bool { return $user->hasPermissionTo('delete_user') && $target->id !== $user->id; }
    public function deleteAny(User $user): bool { return $user->hasPermissionTo('delete_any_user'); }
    public function restore(User $user, User $target): bool { return $user->hasPermissionTo('delete_user'); }
    public function forceDelete(User $user, User $target): bool { return $user->hasPermissionTo('delete_any_user'); }
}
