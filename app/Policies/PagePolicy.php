<?php
namespace App\Policies;
use App\Models\User;
use Modules\CMS\Models\Page;
use Illuminate\Auth\Access\HandlesAuthorization;
class PagePolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user): bool { return $user->hasAnyPermission(['view_any_page', 'view_page', 'create_page', 'update_page', 'delete_page']); }
    public function view(User $user, Page $r): bool { return $user->hasPermissionTo('view_page'); }
    public function create(User $user): bool { return $user->hasPermissionTo('create_page'); }
    public function update(User $user, Page $r): bool { return $user->hasPermissionTo('update_page'); }
    public function delete(User $user, Page $r): bool { return $user->hasPermissionTo('delete_page'); }
    public function deleteAny(User $user): bool { return $user->hasPermissionTo('delete_page'); }
    public function restore(User $user, Page $r): bool { return $user->hasPermissionTo('delete_page'); }
    public function forceDelete(User $user, Page $r): bool { return $user->hasPermissionTo('delete_page'); }
}
