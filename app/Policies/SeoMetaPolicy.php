<?php
namespace App\Policies;
use App\Models\User;
use Modules\SEO\Models\SeoMeta;
use Illuminate\Auth\Access\HandlesAuthorization;
class SeoMetaPolicy
{
    use HandlesAuthorization;
    public function viewAny(User \): bool { return \->hasAnyPermission(['view_seo', 'update_seo']); }
    public function view(User \, SeoMeta \): bool { return \->hasPermissionTo('view_seo'); }
    public function create(User \): bool { return \->hasPermissionTo('update_seo'); }
    public function update(User \, SeoMeta \): bool { return \->hasPermissionTo('update_seo'); }
    public function delete(User \, SeoMeta \): bool { return \->hasPermissionTo('update_seo'); }
    public function deleteAny(User \): bool { return \->hasPermissionTo('update_seo'); }
    public function restore(User \, SeoMeta \): bool { return \->hasPermissionTo('update_seo'); }
    public function forceDelete(User \, SeoMeta \): bool { return \->hasPermissionTo('update_seo'); }
}
