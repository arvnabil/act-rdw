<?php
namespace App\Policies;
use App\Models\User;
use Modules\Campaign\Models\Campaign;
use Illuminate\Auth\Access\HandlesAuthorization;
class CampaignPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user): bool { return $user->hasAnyPermission(['view_any_campaign', 'view_campaign', 'create_campaign', 'update_campaign', 'delete_campaign']); }
    public function view(User $user, Campaign $r): bool { return $user->hasPermissionTo('view_campaign'); }
    public function create(User $user): bool { return $user->hasPermissionTo('create_campaign'); }
    public function update(User $user, Campaign $r): bool { return $user->hasPermissionTo('update_campaign'); }
    public function delete(User $user, Campaign $r): bool { return $user->hasPermissionTo('delete_campaign'); }
    public function deleteAny(User $user): bool { return $user->hasPermissionTo('delete_campaign'); }
    public function restore(User $user, Campaign $r): bool { return $user->hasPermissionTo('delete_campaign'); }
    public function forceDelete(User $user, Campaign $r): bool { return $user->hasPermissionTo('delete_campaign'); }
}
