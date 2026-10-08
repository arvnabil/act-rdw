<?php
namespace App\Policies;
use App\Models\User;
use Modules\Analytics\Models\AnalyticsWhatsapp;
use Illuminate\Auth\Access\HandlesAuthorization;
class AnalyticsWhatsappPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user): bool { return $user->hasAnyPermission(['view_analytics', 'view_whatsapp']); }
    public function view(User $user, AnalyticsWhatsapp $r): bool { return $user->hasAnyPermission(['view_analytics', 'view_whatsapp']); }
    public function create(User $user): bool { return false; }
    public function update(User $user, AnalyticsWhatsapp $r): bool { return false; }
    public function delete(User $user, AnalyticsWhatsapp $r): bool { return false; }
    public function deleteAny(User $user): bool { return false; }
    public function restore(User $user, AnalyticsWhatsapp $r): bool { return false; }
    public function forceDelete(User $user, AnalyticsWhatsapp $r): bool { return false; }
}
