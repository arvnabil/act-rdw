<?php
namespace App\Policies;
use App\Models\User;
use Modules\FormBuilder\Models\FormSubmission;
use Illuminate\Auth\Access\HandlesAuthorization;
class FormSubmissionPolicy
{
    use HandlesAuthorization;
    public function viewAny(User \): bool { return \->hasAnyPermission(['view_any_form', 'view_form', 'create_form', 'update_form', 'delete_form']); }
    public function view(User \, FormSubmission \): bool { return \->hasPermissionTo('view_form'); }
    public function create(User \): bool { return false; }
    public function update(User \, FormSubmission \): bool { return \->hasPermissionTo('update_form'); }
    public function delete(User \, FormSubmission \): bool { return \->hasPermissionTo('delete_form'); }
    public function deleteAny(User \): bool { return \->hasPermissionTo('delete_form'); }
    public function restore(User \, FormSubmission \): bool { return \->hasPermissionTo('delete_form'); }
    public function forceDelete(User \, FormSubmission \): bool { return \->hasPermissionTo('delete_form'); }
}
