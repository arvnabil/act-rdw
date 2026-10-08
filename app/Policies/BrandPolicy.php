<?php
namespace App\Policies;
use App\Models\User;
use Modules\ProductCatalog\Models\Brand;
use Illuminate\Auth\Access\HandlesAuthorization;
class BrandPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user): bool { return $user->hasAnyPermission(['view_any_brand', 'view_brand', 'create_brand', 'update_brand', 'delete_brand']); }
    public function view(User $user, Brand $r): bool { return $user->hasPermissionTo('view_brand'); }
    public function create(User $user): bool { return $user->hasPermissionTo('create_brand'); }
    public function update(User $user, Brand $r): bool { return $user->hasPermissionTo('update_brand'); }
    public function delete(User $user, Brand $r): bool { return $user->hasPermissionTo('delete_brand'); }
    public function deleteAny(User $user): bool { return $user->hasPermissionTo('delete_brand'); }
    public function restore(User $user, Brand $r): bool { return $user->hasPermissionTo('delete_brand'); }
    public function forceDelete(User $user, Brand $r): bool { return $user->hasPermissionTo('delete_brand'); }
}
