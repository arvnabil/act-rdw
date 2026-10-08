<?php
namespace App\Policies;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;
class RolePolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user): bool { return $user->hasAnyPermission(['view_any_role', 'view_role', 'create_role', 'update_role', 'delete_role']); }
    public function view(User $user, Role $r): bool { return $user->hasPermissionTo('view_role'); }
    public function create(User $user): bool { return $user->hasPermissionTo('create_role'); }
    public function update(User $user, Role $r): bool { return $user->hasPermissionTo('update_role'); }
    public function delete(User $user, Role $r): bool { return $user->hasPermissionTo('delete_role'); }
    public function deleteAny(User $user): bool { return $user->hasPermissionTo('delete_role'); }
    public function restore(User $user, Role $r): bool { return $user->hasPermissionTo('delete_role'); }
    public function forceDelete(User $user, Role $r): bool { return $user->hasPermissionTo('delete_role'); }
}
