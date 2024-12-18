<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TransportFee;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransportFeePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_transport::fee') || (employeeHasPermission('view_any_transport::fee'));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TransportFee $transportFee): bool
    {
        return $user->can('view_transport::fee') || (employeeHasPermission('view_transport::fee'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_transport::fee') || (employeeHasPermission('create_transport::fee'));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TransportFee $transportFee): bool
    {
        return $user->can('update_transport::fee') || (employeeHasPermission('update_transport::fee'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TransportFee $transportFee): bool
    {
        return $user->can('delete_transport::fee') || (employeeHasPermission('delete_transport::fee'));
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_transport::fee') || (employeeHasPermission('delete_any_transport::fee'));
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, TransportFee $transportFee): bool
    {
        return $user->can('force_delete_transport::fee');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_transport::fee');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, TransportFee $transportFee): bool
    {
        return $user->can('restore_transport::fee') || (employeeHasPermission('restore_transport::fee'));
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_transport::fee');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, TransportFee $transportFee): bool
    {
        return $user->can('replicate_transport::fee');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_transport::fee');
    }
}
