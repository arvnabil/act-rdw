<?php

namespace App\Policies;

use App\Models\User;
use Modules\ProductCatalog\Models\Brand;
use Illuminate\Auth\Access\HandlesAuthorization;

class BrandPolicy
{
    use HandlesAuthorization;

    public function viewAny(User ): bool
    {
        return ->hasAnyPermission(['view_any_brand', 'view_brand', 'create_brand', 'update_brand', 'delete_brand']);
    }

    public function view(User , Brand ): bool
    {
        return ->hasPermissionTo('view_brand');
    }

    public function create(User ): bool
    {
        return ->hasPermissionTo('create_brand');
    }

    public function update(User , Brand ): bool
    {
        return ->hasPermissionTo('update_brand');
    }

    public function delete(User , Brand ): bool
    {
        return ->hasPermissionTo('delete_brand');
    }

    public function deleteAny(User ): bool
    {
        return ->hasPermissionTo('delete_brand');
    }

    public function restore(User , Brand ): bool
    {
        return ->hasPermissionTo('delete_brand');
    }

    public function forceDelete(User , Brand ): bool
    {
        return ->hasPermissionTo('delete_brand');
    }
}
