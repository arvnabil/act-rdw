<?php
namespace App\Policies;
use App\Models\User;
use Modules\News\Models\NewsTag;
use Illuminate\Auth\Access\HandlesAuthorization;
class NewsTagPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user): bool { return $user->hasAnyPermission(['view_any_news_tag', 'view_news_tag', 'create_news_tag', 'update_news_tag', 'delete_news_tag']); }
    public function view(User $user, NewsTag $r): bool { return $user->hasPermissionTo('view_news_tag'); }
    public function create(User $user): bool { return $user->hasPermissionTo('create_news_tag'); }
    public function update(User $user, NewsTag $r): bool { return $user->hasPermissionTo('update_news_tag'); }
    public function delete(User $user, NewsTag $r): bool { return $user->hasPermissionTo('delete_news_tag'); }
    public function deleteAny(User $user): bool { return $user->hasPermissionTo('delete_news_tag'); }
    public function restore(User $user, NewsTag $r): bool { return $user->hasPermissionTo('delete_news_tag'); }
    public function forceDelete(User $user, NewsTag $r): bool { return $user->hasPermissionTo('delete_news_tag'); }
}
