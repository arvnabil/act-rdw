<?php
namespace App\Policies;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;
class RolePolicy
{
    use HandlesAuthorization;
    public function viewAny(User \): bool { return \->hasAnyPermission(['view_any_role', 'view_role', 'create_role', 'update_role', 'delete_role']); }
    public function view(User \, Role \): bool { return \->hasPermissionTo('view_role'); }
    public function create(User \): bool { return \->hasPermissionTo('create_role'); }
    public function update(User \, Role \): bool { return \->hasPermissionTo('update_role'); }
    public function delete(User \, Role \): bool { return \->hasPermissionTo('delete_role'); }
    public function deleteAny(User \): bool { return \->hasPermissionTo('delete_role'); }
    public function restore(User \, Role \): bool { return \->hasPermissionTo('delete_role'); }
    public function forceDelete(User \, Role \): bool { return \->hasPermissionTo('delete_role'); }
}
