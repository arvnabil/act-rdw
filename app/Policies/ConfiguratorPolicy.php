<?php
namespace App\Policies;
use App\Models\User;
use Modules\Services\Models\Configurator;
use Illuminate\Auth\Access\HandlesAuthorization;
class ConfiguratorPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user): bool { return $user->hasAnyPermission(['view_any_service', 'view_service', 'create_service', 'update_service', 'delete_service']); }
    public function view(User $user, Configurator $r): bool { return $user->hasPermissionTo('view_service'); }
    public function create(User $user): bool { return $user->hasPermissionTo('create_service'); }
    public function update(User $user, Configurator $r): bool { return $user->hasPermissionTo('update_service'); }
    public function delete(User $user, Configurator $r): bool { return $user->hasPermissionTo('delete_service'); }
    public function deleteAny(User $user): bool { return $user->hasPermissionTo('delete_service'); }
    public function restore(User $user, Configurator $r): bool { return $user->hasPermissionTo('delete_service'); }
    public function forceDelete(User $user, Configurator $r): bool { return $user->hasPermissionTo('delete_service'); }
}
