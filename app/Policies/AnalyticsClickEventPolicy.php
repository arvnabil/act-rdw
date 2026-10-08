<?php
namespace App\Policies;
use App\Models\User;
use Modules\Analytics\Models\AnalyticsClickEvent;
use Illuminate\Auth\Access\HandlesAuthorization;
class AnalyticsClickEventPolicy
{
    use HandlesAuthorization;
    public function viewAny(User \): bool { return \->hasAnyPermission(['view_analytics']); }
    public function view(User \, AnalyticsClickEvent \): bool { return \->hasPermissionTo('view_analytics'); }
    public function create(User \): bool { return false; }
    public function update(User \, AnalyticsClickEvent \): bool { return false; }
    public function delete(User \, AnalyticsClickEvent \): bool { return false; }
    public function deleteAny(User \): bool { return false; }
    public function restore(User \, AnalyticsClickEvent \): bool { return false; }
    public function forceDelete(User \, AnalyticsClickEvent \): bool { return false; }
}
