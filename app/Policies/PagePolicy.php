<?php

namespace App\Policies;

use App\Models\User;
use Modules\CMS\Models\Page;
use Illuminate\Auth\Access\HandlesAuthorization;

class PagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User ): bool
    {
        return ->hasAnyPermission(['view_any_page', 'view_page', 'create_page', 'update_page', 'delete_page', 'delete_any_page']);
    }

    public function view(User , Page ): bool
    {
        return ->hasPermissionTo('view_page');
    }

    public function create(User ): bool
    {
        return ->hasPermissionTo('create_page');
    }

    public function update(User , Page ): bool
    {
        return ->hasPermissionTo('update_page');
    }

    public function delete(User , Page ): bool
    {
        return ->hasPermissionTo('delete_page');
    }

    public function deleteAny(User ): bool
    {
        return ->hasPermissionTo('delete_any_page');
    }

    public function restore(User , Page ): bool
    {
        return ->hasPermissionTo('delete_page');
    }

    public function forceDelete(User , Page ): bool
    {
        return ->hasPermissionTo('delete_any_page');
    }
}
