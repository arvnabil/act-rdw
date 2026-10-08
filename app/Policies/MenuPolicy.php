<?php
namespace App\Policies;
use App\Models\User;
use Modules\Menu\Models\Menu;
use Illuminate\Auth\Access\HandlesAuthorization;
class MenuPolicy
{
    use HandlesAuthorization;
    public function viewAny(User \): bool { return \->hasAnyPermission(['view_any_menu', 'view_menu', 'create_menu', 'update_menu', 'delete_menu']); }
    public function view(User \, Menu \): bool { return \->hasPermissionTo('view_menu'); }
    public function create(User \): bool { return \->hasPermissionTo('create_menu'); }
    public function update(User \, Menu \): bool { return \->hasPermissionTo('update_menu'); }
    public function delete(User \, Menu \): bool { return \->hasPermissionTo('delete_menu'); }
    public function deleteAny(User \): bool { return \->hasPermissionTo('delete_menu'); }
    public function restore(User \, Menu \): bool { return \->hasPermissionTo('delete_menu'); }
    public function forceDelete(User \, Menu \): bool { return \->hasPermissionTo('delete_menu'); }
}
