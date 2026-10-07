<?php

namespace App\Policies;

use App\Models\User;
use Modules\Projects\Models\Project;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_project');
    }

    public function view(User $user, Project $project): bool
    {
        return $user->hasPermissionTo('view_project');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_project');
    }

    public function update(User $user, Project $project): bool
    {
        return $user->hasPermissionTo('update_project');
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->hasPermissionTo('delete_project');
    }

    public function restore(User $user, Project $project): bool
    {
        return $user->hasPermissionTo('delete_project');
    }

    public function forceDelete(User $user, Project $project): bool
    {
        return $user->hasPermissionTo('delete_any_project');
    }
}
