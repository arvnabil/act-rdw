<?php

namespace App\Policies;

use App\Models\User;
use Modules\ProductCatalog\Models\ProductCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User ): bool
    {
        return ->hasAnyPermission(['view_any_product', 'view_product', 'create_product', 'update_product', 'delete_product']);
    }

    public function view(User , ProductCategory ): bool
    {
        return ->hasPermissionTo('view_product');
    }

    public function create(User ): bool
    {
        return ->hasPermissionTo('create_product');
    }

    public function update(User , ProductCategory ): bool
    {
        return ->hasPermissionTo('update_product');
    }

    public function delete(User , ProductCategory ): bool
    {
        return ->hasPermissionTo('delete_product');
    }

    public function deleteAny(User ): bool
    {
        return ->hasPermissionTo('delete_any_product');
    }

    public function restore(User , ProductCategory ): bool
    {
        return ->hasPermissionTo('delete_product');
    }

    public function forceDelete(User , ProductCategory ): bool
    {
        return ->hasPermissionTo('delete_any_product');
    }
}
