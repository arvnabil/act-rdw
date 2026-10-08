<?php
namespace App\Policies;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
class UserPolicy
{
    use HandlesAuthorization;
    public function viewAny(User \): bool { return \->hasAnyPermission(['view_any_user', 'view_user', 'create_user', 'update_user', 'delete_user', 'delete_any_user']); }
    public function view(User \, User \): bool { return \->hasPermissionTo('view_user'); }
    public function create(User \): bool { return \->hasPermissionTo('create_user'); }
    public function update(User \, User \): bool { return \->hasPermissionTo('update_user'); }
    public function delete(User \, User \): bool { return \->hasPermissionTo('delete_user') && \->id !== \->id; }
    public function deleteAny(User \): bool { return \->hasPermissionTo('delete_any_user'); }
    public function restore(User \, User \): bool { return \->hasPermissionTo('delete_user'); }
    public function forceDelete(User \, User \): bool { return \->hasPermissionTo('delete_any_user'); }
}
