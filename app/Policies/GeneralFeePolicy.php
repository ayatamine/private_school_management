<?php

namespace App\Policies;

use App\Models\User;
use App\Models\GeneralFee;
use Illuminate\Auth\Access\HandlesAuthorization;

class GeneralFeePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_general::fee') || (employeeHasPermission('view_any_general::fee'));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, GeneralFee $generalFee): bool
    {
        return $user->can('view_general::fee') || (employeeHasPermission('view_general::fee'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_general::fee') || (employeeHasPermission('create_general::fee'));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, GeneralFee $generalFee): bool
    {
        return $user->can('update_general::fee') || (employeeHasPermission('update_general::fee'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, GeneralFee $generalFee): bool
    {
        return $user->can('delete_general::fee') || (employeeHasPermission('delete_general::fee'));
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_general::fee') || (employeeHasPermission('delete_any_general::fee'));
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, GeneralFee $generalFee): bool
    {
        return $user->can('force_delete_general::fee') || (employeeHasPermission('force_delete_general::fee'));
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_general::fee') || (employeeHasPermission('force_delete_any_general::fee'));
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, GeneralFee $generalFee): bool
    {
        return $user->can('restore_general::fee') || (employeeHasPermission('restore_general::fee'));
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_general::fee') || (employeeHasPermission('restore_any_general::fee'));
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, GeneralFee $generalFee): bool
    {
        return $user->can('replicate_general::fee') || (employeeHasPermission('replicate_general::fee'));
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_general::fee') || (employeeHasPermission('reorder_general::fee'));
    }
}
