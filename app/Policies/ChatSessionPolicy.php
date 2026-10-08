<?php
namespace App\Policies;
use App\Models\User;
use Modules\AI\Models\ChatSession;
use Illuminate\Auth\Access\HandlesAuthorization;
class ChatSessionPolicy
{
    use HandlesAuthorization;
    public function viewAny(User \): bool { return \->hasAnyPermission(['view_settings', 'update_settings']); }
    public function view(User \, ChatSession \): bool { return \->hasAnyPermission(['view_settings', 'update_settings']); }
    public function create(User \): bool { return false; }
    public function update(User \, ChatSession \): bool { return false; }
    public function delete(User \, ChatSession \): bool { return \->hasPermissionTo('update_settings'); }
    public function deleteAny(User \): bool { return \->hasPermissionTo('update_settings'); }
    public function restore(User \, ChatSession \): bool { return false; }
    public function forceDelete(User \, ChatSession \): bool { return false; }
}
