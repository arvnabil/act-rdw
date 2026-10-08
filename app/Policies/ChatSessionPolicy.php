<?php
namespace App\Policies;
use App\Models\User;
use Modules\AI\Models\ChatSession;
use Illuminate\Auth\Access\HandlesAuthorization;
class ChatSessionPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user): bool { return $user->hasAnyPermission(['view_settings', 'update_settings']); }
    public function view(User $user, ChatSession $r): bool { return $user->hasAnyPermission(['view_settings', 'update_settings']); }
    public function create(User $user): bool { return false; }
    public function update(User $user, ChatSession $r): bool { return false; }
    public function delete(User $user, ChatSession $r): bool { return $user->hasPermissionTo('update_settings'); }
    public function deleteAny(User $user): bool { return $user->hasPermissionTo('update_settings'); }
    public function restore(User $user, ChatSession $r): bool { return false; }
    public function forceDelete(User $user, ChatSession $r): bool { return false; }
}
