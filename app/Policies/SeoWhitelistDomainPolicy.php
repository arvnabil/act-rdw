<?php
namespace App\Policies;
use App\Models\User;
use Modules\SEO\Models\SeoWhitelistDomain;
use Illuminate\Auth\Access\HandlesAuthorization;
class SeoWhitelistDomainPolicy
{
    use HandlesAuthorization;
    public function viewAny(User \): bool { return \->hasAnyPermission(['view_seo', 'update_seo']); }
    public function view(User \, SeoWhitelistDomain \): bool { return \->hasPermissionTo('view_seo'); }
    public function create(User \): bool { return \->hasPermissionTo('update_seo'); }
    public function update(User \, SeoWhitelistDomain \): bool { return \->hasPermissionTo('update_seo'); }
    public function delete(User \, SeoWhitelistDomain \): bool { return \->hasPermissionTo('update_seo'); }
    public function deleteAny(User \): bool { return \->hasPermissionTo('update_seo'); }
    public function restore(User \, SeoWhitelistDomain \): bool { return \->hasPermissionTo('update_seo'); }
    public function forceDelete(User \, SeoWhitelistDomain \): bool { return \->hasPermissionTo('update_seo'); }
}
