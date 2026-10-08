<?php
namespace App\Policies;
use App\Models\User;
use Modules\Settings\Models\Setting;
use Illuminate\Auth\Access\HandlesAuthorization;
class SettingPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user): bool { return $user->hasAnyPermission(['view_settings', 'update_settings']); }
    public function view(User $user, Setting $r): bool { return $user->hasPermissionTo('view_settings'); }
    public function create(User $user): bool { return $user->hasPermissionTo('update_settings'); }
    public function update(User $user, Setting $r): bool { return $user->hasPermissionTo('update_settings'); }
    public function delete(User $user, Setting $r): bool { return $user->hasPermissionTo('update_settings'); }
    public function deleteAny(User $user): bool { return $user->hasPermissionTo('update_settings'); }
    public function restore(User $user, Setting $r): bool { return $user->hasPermissionTo('update_settings'); }
    public function forceDelete(User $user, Setting $r): bool { return $user->hasPermissionTo('update_settings'); }
}
