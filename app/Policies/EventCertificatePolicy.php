<?php
namespace App\Policies;
use App\Models\User;
use Modules\Events\Models\EventCertificate;
use Illuminate\Auth\Access\HandlesAuthorization;
class EventCertificatePolicy
{
    use HandlesAuthorization;
    public function viewAny(User \): bool { return \->hasAnyPermission(['view_any_event', 'view_event', 'create_event', 'update_event', 'delete_event']); }
    public function view(User \, EventCertificate \): bool { return \->hasPermissionTo('view_event'); }
    public function create(User \): bool { return \->hasPermissionTo('create_event'); }
    public function update(User \, EventCertificate \): bool { return \->hasPermissionTo('update_event'); }
    public function delete(User \, EventCertificate \): bool { return \->hasPermissionTo('delete_event'); }
    public function deleteAny(User \): bool { return \->hasPermissionTo('delete_any_event'); }
    public function restore(User \, EventCertificate \): bool { return \->hasPermissionTo('delete_event'); }
    public function forceDelete(User \, EventCertificate \): bool { return \->hasPermissionTo('delete_any_event'); }
}
