<?php

namespace App\Policies;

use App\Models\User;
use Modules\Campaign\Models\Campaign;
use Illuminate\Auth\Access\HandlesAuthorization;

class CampaignPolicy
{
    use HandlesAuthorization;

    public function viewAny(User ): bool
    {
        return ->hasAnyPermission(['view_any_campaign', 'view_campaign', 'create_campaign', 'update_campaign', 'delete_campaign']);
    }

    public function view(User , Campaign ): bool
    {
        return ->hasPermissionTo('view_campaign');
    }

    public function create(User ): bool
    {
        return ->hasPermissionTo('create_campaign');
    }

    public function update(User , Campaign ): bool
    {
        return ->hasPermissionTo('update_campaign');
    }

    public function delete(User , Campaign ): bool
    {
        return ->hasPermissionTo('delete_campaign');
    }

    public function deleteAny(User ): bool
    {
        return ->hasPermissionTo('delete_campaign');
    }

    public function restore(User , Campaign ): bool
    {
        return ->hasPermissionTo('delete_campaign');
    }

    public function forceDelete(User , Campaign ): bool
    {
        return ->hasPermissionTo('delete_campaign');
    }
}
