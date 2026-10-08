<?php
namespace App\Policies;
use App\Models\User;
use Modules\ProductCatalog\Models\ProductCategory;
use Illuminate\Auth\Access\HandlesAuthorization;
class ProductCategoryPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user): bool { return $user->hasAnyPermission(['view_any_product', 'view_product', 'create_product', 'update_product', 'delete_product']); }
    public function view(User $user, ProductCategory $r): bool { return $user->hasPermissionTo('view_product'); }
    public function create(User $user): bool { return $user->hasPermissionTo('create_product'); }
    public function update(User $user, ProductCategory $r): bool { return $user->hasPermissionTo('update_product'); }
    public function delete(User $user, ProductCategory $r): bool { return $user->hasPermissionTo('delete_product'); }
    public function deleteAny(User $user): bool { return $user->hasPermissionTo('delete_product'); }
    public function restore(User $user, ProductCategory $r): bool { return $user->hasPermissionTo('delete_product'); }
    public function forceDelete(User $user, ProductCategory $r): bool { return $user->hasPermissionTo('delete_product'); }
}
