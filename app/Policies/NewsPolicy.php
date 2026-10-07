<?php

namespace App\Policies;

use App\Models\User;
use Modules\News\Models\News;
use Illuminate\Auth\Access\HandlesAuthorization;

class NewsPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_news');
    }

    public function view(User $user, News $news): bool
    {
        return $user->hasPermissionTo('view_news');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_news');
    }

    public function update(User $user, News $news): bool
    {
        return $user->hasPermissionTo('update_news');
    }

    public function delete(User $user, News $news): bool
    {
        return $user->hasPermissionTo('delete_news');
    }

    public function restore(User $user, News $news): bool
    {
        return $user->hasPermissionTo('delete_news');
    }

    public function forceDelete(User $user, News $news): bool
    {
        return $user->hasPermissionTo('delete_any_news');
    }
}
