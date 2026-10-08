<?php
namespace App\Policies;
use App\Models\User;
use Modules\News\Models\NewsTag;
use Illuminate\Auth\Access\HandlesAuthorization;
class NewsTagPolicy
{
    use HandlesAuthorization;
    public function viewAny(User \): bool { return \->hasAnyPermission(['view_any_news_tag', 'view_news_tag', 'create_news_tag', 'update_news_tag', 'delete_news_tag']); }
    public function view(User \, NewsTag \): bool { return \->hasPermissionTo('view_news_tag'); }
    public function create(User \): bool { return \->hasPermissionTo('create_news_tag'); }
    public function update(User \, NewsTag \): bool { return \->hasPermissionTo('update_news_tag'); }
    public function delete(User \, NewsTag \): bool { return \->hasPermissionTo('delete_news_tag'); }
    public function deleteAny(User \): bool { return \->hasPermissionTo('delete_news_tag'); }
    public function restore(User \, NewsTag \): bool { return \->hasPermissionTo('delete_news_tag'); }
    public function forceDelete(User \, NewsTag \): bool { return \->hasPermissionTo('delete_news_tag'); }
}
