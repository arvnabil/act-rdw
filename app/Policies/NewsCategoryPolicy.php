<?php
namespace App\Policies;
use App\Models\User;
use Modules\News\Models\NewsCategory;
use Illuminate\Auth\Access\HandlesAuthorization;
class NewsCategoryPolicy
{
    use HandlesAuthorization;
    public function viewAny(User \): bool { return \->hasAnyPermission(['view_any_news_category', 'view_news_category', 'create_news_category', 'update_news_category', 'delete_news_category']); }
    public function view(User \, NewsCategory \): bool { return \->hasPermissionTo('view_news_category'); }
    public function create(User \): bool { return \->hasPermissionTo('create_news_category'); }
    public function update(User \, NewsCategory \): bool { return \->hasPermissionTo('update_news_category'); }
    public function delete(User \, NewsCategory \): bool { return \->hasPermissionTo('delete_news_category'); }
    public function deleteAny(User \): bool { return \->hasPermissionTo('delete_news_category'); }
    public function restore(User \, NewsCategory \): bool { return \->hasPermissionTo('delete_news_category'); }
    public function forceDelete(User \, NewsCategory \): bool { return \->hasPermissionTo('delete_news_category'); }
}
