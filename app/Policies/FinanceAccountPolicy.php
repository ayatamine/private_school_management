<?php

namespace App\Policies;

use App\Models\User;
use App\Models\FinanceAccount;
use Illuminate\Auth\Access\HandlesAuthorization;

class FinanceAccountPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_finance::account') || (employeeHasPermission('view_any_finance::account'));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FinanceAccount $financeAccount): bool
    {
        return $user->can('view_finance::account') || (employeeHasPermission('view_finance::account'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_finance::account') || (employeeHasPermission('create_finance::account'));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FinanceAccount $financeAccount): bool
    {
        return $user->can('update_finance::account') || (employeeHasPermission('update_finance::account'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FinanceAccount $financeAccount): bool
    {
        return $user->can('delete_finance::account') || (employeeHasPermission('delete_finance::account'));
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_finance::account') || (employeeHasPermission('delete_any_finance::account'));
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, FinanceAccount $financeAccount): bool
    {
        return $user->can('force_delete_finance::account');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_finance::account');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, FinanceAccount $financeAccount): bool
    {
        return $user->can('restore_finance::account') || (employeeHasPermission('restore_finance::account'));
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_finance::account');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, FinanceAccount $financeAccount): bool
    {
        return $user->can('replicate_finance::account') || (employeeHasPermission('replicate_finance::account'));
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_finance::account');
    }
}
