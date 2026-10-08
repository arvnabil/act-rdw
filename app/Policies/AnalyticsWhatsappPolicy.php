<?php
namespace App\Policies;
use App\Models\User;
use Modules\Analytics\Models\AnalyticsWhatsapp;
use Illuminate\Auth\Access\HandlesAuthorization;
class AnalyticsWhatsappPolicy
{
    use HandlesAuthorization;
    public function viewAny(User \): bool { return \->hasAnyPermission(['view_analytics', 'view_whatsapp']); }
    public function view(User \, AnalyticsWhatsapp \): bool { return \->hasAnyPermission(['view_analytics', 'view_whatsapp']); }
    public function create(User \): bool { return false; }
    public function update(User \, AnalyticsWhatsapp \): bool { return false; }
    public function delete(User \, AnalyticsWhatsapp \): bool { return false; }
    public function deleteAny(User \): bool { return false; }
    public function restore(User \, AnalyticsWhatsapp \): bool { return false; }
    public function forceDelete(User \, AnalyticsWhatsapp \): bool { return false; }
}
