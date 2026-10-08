<?php
namespace App\Policies;
use App\Models\User;
use Modules\Menu\Models\Menu;
use Illuminate\Auth\Access\HandlesAuthorization;
class MenuPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user): bool { return $user->hasAnyPermission(['view_any_menu', 'view_menu', 'create_menu', 'update_menu', 'delete_menu']); }
    public function view(User $user, Menu $r): bool { return $user->hasPermissionTo('view_menu'); }
    public function create(User $user): bool { return $user->hasPermissionTo('create_menu'); }
    public function update(User $user, Menu $r): bool { return $user->hasPermissionTo('update_menu'); }
    public function delete(User $user, Menu $r): bool { return $user->hasPermissionTo('delete_menu'); }
    public function deleteAny(User $user): bool { return $user->hasPermissionTo('delete_menu'); }
    public function restore(User $user, Menu $r): bool { return $user->hasPermissionTo('delete_menu'); }
    public function forceDelete(User $user, Menu $r): bool { return $user->hasPermissionTo('delete_menu'); }
}
