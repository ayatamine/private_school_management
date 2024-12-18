<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Transport;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransportPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_transport::termination') || (employeeHasPermission('view_any_transport::termination'));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Transport $transport): bool
    {
        return $user->can('view_transport::termination') || (employeeHasPermission('view_transport::termination'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_transport::termination') || (employeeHasPermission('create_transport::termination'));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Transport $transport): bool
    {
        return $user->can('update_transport::termination') || (employeeHasPermission('update_transport::termination'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Transport $transport): bool
    {
        return $user->can('delete_transport::termination') || (employeeHasPermission('delete_transport::termination'));
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_transport::termination');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Transport $transport): bool
    {
        return $user->can('force_delete_transport::termination');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_transport::termination');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Transport $transport): bool
    {
        return $user->can('restore_transport::termination')  || (employeeHasPermission('restore_transport::termination'));
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_transport::termination');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Transport $transport): bool
    {
        return $user->can('replicate_transport::termination');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_transport::termination');
    }
}
