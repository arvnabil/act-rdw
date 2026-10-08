<?php
namespace App\Policies;
use App\Models\User;
use Modules\Settings\Models\ApiKey;
use Illuminate\Auth\Access\HandlesAuthorization;
class ApiKeyPolicy
{
    use HandlesAuthorization;
    public function viewAny(User \): bool { return \->hasAnyPermission(['view_settings', 'update_settings']); }
    public function view(User \, ApiKey \): bool { return \->hasPermissionTo('view_settings'); }
    public function create(User \): bool { return \->hasPermissionTo('update_settings'); }
    public function update(User \, ApiKey \): bool { return \->hasPermissionTo('update_settings'); }
    public function delete(User \, ApiKey \): bool { return \->hasPermissionTo('update_settings'); }
    public function deleteAny(User \): bool { return \->hasPermissionTo('update_settings'); }
    public function restore(User \, ApiKey \): bool { return \->hasPermissionTo('update_settings'); }
    public function forceDelete(User \, ApiKey \): bool { return \->hasPermissionTo('update_settings'); }
}
