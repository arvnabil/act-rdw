<?php
namespace App\Policies;
use App\Models\User;
use Modules\Analytics\Models\AnalyticsClickEvent;
use Illuminate\Auth\Access\HandlesAuthorization;
class AnalyticsClickEventPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user): bool { return $user->hasAnyPermission(['view_analytics']); }
    public function view(User $user, AnalyticsClickEvent $r): bool { return $user->hasPermissionTo('view_analytics'); }
    public function create(User $user): bool { return false; }
    public function update(User $user, AnalyticsClickEvent $r): bool { return false; }
    public function delete(User $user, AnalyticsClickEvent $r): bool { return false; }
    public function deleteAny(User $user): bool { return false; }
    public function restore(User $user, AnalyticsClickEvent $r): bool { return false; }
    public function forceDelete(User $user, AnalyticsClickEvent $r): bool { return false; }
}
