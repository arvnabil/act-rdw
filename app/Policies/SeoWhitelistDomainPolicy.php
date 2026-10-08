<?php
namespace App\Policies;
use App\Models\User;
use Modules\SEO\Models\SeoWhitelistDomain;
use Illuminate\Auth\Access\HandlesAuthorization;
class SeoWhitelistDomainPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user): bool { return $user->hasAnyPermission(['view_seo', 'update_seo']); }
    public function view(User $user, SeoWhitelistDomain $r): bool { return $user->hasPermissionTo('view_seo'); }
    public function create(User $user): bool { return $user->hasPermissionTo('update_seo'); }
    public function update(User $user, SeoWhitelistDomain $r): bool { return $user->hasPermissionTo('update_seo'); }
    public function delete(User $user, SeoWhitelistDomain $r): bool { return $user->hasPermissionTo('update_seo'); }
    public function deleteAny(User $user): bool { return $user->hasPermissionTo('update_seo'); }
    public function restore(User $user, SeoWhitelistDomain $r): bool { return $user->hasPermissionTo('update_seo'); }
    public function forceDelete(User $user, SeoWhitelistDomain $r): bool { return $user->hasPermissionTo('update_seo'); }
}
