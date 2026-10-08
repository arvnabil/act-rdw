<?php
namespace App\Policies;
use App\Models\User;
use Modules\Settings\Models\ApiKey;
use Illuminate\Auth\Access\HandlesAuthorization;
class ApiKeyPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user): bool { return $user->hasAnyPermission(['view_settings', 'update_settings']); }
    public function view(User $user, ApiKey $r): bool { return $user->hasPermissionTo('view_settings'); }
    public function create(User $user): bool { return $user->hasPermissionTo('update_settings'); }
    public function update(User $user, ApiKey $r): bool { return $user->hasPermissionTo('update_settings'); }
    public function delete(User $user, ApiKey $r): bool { return $user->hasPermissionTo('update_settings'); }
    public function deleteAny(User $user): bool { return $user->hasPermissionTo('update_settings'); }
    public function restore(User $user, ApiKey $r): bool { return $user->hasPermissionTo('update_settings'); }
    public function forceDelete(User $user, ApiKey $r): bool { return $user->hasPermissionTo('update_settings'); }
}
