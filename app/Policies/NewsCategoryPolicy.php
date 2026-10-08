<?php
namespace App\Policies;
use App\Models\User;
use Modules\News\Models\NewsCategory;
use Illuminate\Auth\Access\HandlesAuthorization;
class NewsCategoryPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user): bool { return $user->hasAnyPermission(['view_any_news_category', 'view_news_category', 'create_news_category', 'update_news_category', 'delete_news_category']); }
    public function view(User $user, NewsCategory $r): bool { return $user->hasPermissionTo('view_news_category'); }
    public function create(User $user): bool { return $user->hasPermissionTo('create_news_category'); }
    public function update(User $user, NewsCategory $r): bool { return $user->hasPermissionTo('update_news_category'); }
    public function delete(User $user, NewsCategory $r): bool { return $user->hasPermissionTo('delete_news_category'); }
    public function deleteAny(User $user): bool { return $user->hasPermissionTo('delete_news_category'); }
    public function restore(User $user, NewsCategory $r): bool { return $user->hasPermissionTo('delete_news_category'); }
    public function forceDelete(User $user, NewsCategory $r): bool { return $user->hasPermissionTo('delete_news_category'); }
}
