<?php
namespace App\Policies;
use App\Models\User;
use Modules\Clients\Models\Client;
use Illuminate\Auth\Access\HandlesAuthorization;
class ClientPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user): bool { return $user->hasAnyPermission(['view_any_client', 'view_client', 'create_client', 'update_client', 'delete_client']); }
    public function view(User $user, Client $r): bool { return $user->hasPermissionTo('view_client'); }
    public function create(User $user): bool { return $user->hasPermissionTo('create_client'); }
    public function update(User $user, Client $r): bool { return $user->hasPermissionTo('update_client'); }
    public function delete(User $user, Client $r): bool { return $user->hasPermissionTo('delete_client'); }
    public function deleteAny(User $user): bool { return $user->hasPermissionTo('delete_client'); }
    public function restore(User $user, Client $r): bool { return $user->hasPermissionTo('delete_client'); }
    public function forceDelete(User $user, Client $r): bool { return $user->hasPermissionTo('delete_client'); }
}
