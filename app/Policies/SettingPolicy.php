<?php
namespace App\Policies;
use App\Models\User;
use Modules\Settings\Models\Setting;
use Illuminate\Auth\Access\HandlesAuthorization;
class SettingPolicy
{
    use HandlesAuthorization;
    public function viewAny(User \): bool { return \->hasAnyPermission(['view_settings', 'update_settings']); }
    public function view(User \, Setting \): bool { return \->hasPermissionTo('view_settings'); }
    public function create(User \): bool { return \->hasPermissionTo('update_settings'); }
    public function update(User \, Setting \): bool { return \->hasPermissionTo('update_settings'); }
    public function delete(User \, Setting \): bool { return \->hasPermissionTo('update_settings'); }
    public function deleteAny(User \): bool { return \->hasPermissionTo('update_settings'); }
    public function restore(User \, Setting \): bool { return \->hasPermissionTo('update_settings'); }
    public function forceDelete(User \, Setting \): bool { return \->hasPermissionTo('update_settings'); }
}
