<?php
namespace App\Policies;
use App\Models\User;
use Modules\FormBuilder\Models\FormSubmission;
use Illuminate\Auth\Access\HandlesAuthorization;
class FormSubmissionPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user): bool { return $user->hasAnyPermission(['view_any_form', 'view_form', 'create_form', 'update_form', 'delete_form']); }
    public function view(User $user, FormSubmission $r): bool { return $user->hasPermissionTo('view_form'); }
    public function create(User $user): bool { return false; }
    public function update(User $user, FormSubmission $r): bool { return $user->hasPermissionTo('update_form'); }
    public function delete(User $user, FormSubmission $r): bool { return $user->hasPermissionTo('delete_form'); }
    public function deleteAny(User $user): bool { return $user->hasPermissionTo('delete_form'); }
    public function restore(User $user, FormSubmission $r): bool { return $user->hasPermissionTo('delete_form'); }
    public function forceDelete(User $user, FormSubmission $r): bool { return $user->hasPermissionTo('delete_form'); }
}
