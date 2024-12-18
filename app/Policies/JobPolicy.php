<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Job;
use Illuminate\Auth\Access\HandlesAuthorization;

class JobPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_job')  || (employeeHasPermission('view_any_job'));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Job $job): bool
    {
        return $user->can('view_job')  || (employeeHasPermission('view_job'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_job')  || (employeeHasPermission('create_job'));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Job $job): bool
    {
        return $user->can('update_job')  || (employeeHasPermission('update_job'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Job $job): bool
    {
        return $user->can('delete_job')  || (employeeHasPermission('delete_job'));
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_job')  || (employeeHasPermission('delete_any_job'));
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Job $job): bool
    {
        return $user->can('force_delete_job')  || (employeeHasPermission('force_delete_job'));
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_job')  || (employeeHasPermission('force_delete_any_job'));
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Job $job): bool
    {
        return $user->can('restore_job')  || (employeeHasPermission('restore_job'));
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_job')  || (employeeHasPermission('restore_any_job'));
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Job $job): bool
    {
        return $user->can('replicate_job')  || (employeeHasPermission('replicate_job'));
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_job')  || (employeeHasPermission('reorder_job'));
    }
}
