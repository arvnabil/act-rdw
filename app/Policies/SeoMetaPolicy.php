<?php
namespace App\Policies;
use App\Models\User;
use Modules\SEO\Models\SeoMeta;
use Illuminate\Auth\Access\HandlesAuthorization;
class SeoMetaPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user): bool { return $user->hasAnyPermission(['view_seo', 'update_seo']); }
    public function view(User $user, SeoMeta $r): bool { return $user->hasPermissionTo('view_seo'); }
    public function create(User $user): bool { return $user->hasPermissionTo('update_seo'); }
    public function update(User $user, SeoMeta $r): bool { return $user->hasPermissionTo('update_seo'); }
    public function delete(User $user, SeoMeta $r): bool { return $user->hasPermissionTo('update_seo'); }
    public function deleteAny(User $user): bool { return $user->hasPermissionTo('update_seo'); }
    public function restore(User $user, SeoMeta $r): bool { return $user->hasPermissionTo('update_seo'); }
    public function forceDelete(User $user, SeoMeta $r): bool { return $user->hasPermissionTo('update_seo'); }
}
