<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Expense;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExpensePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_expense') || (employeeHasPermission('view_any_expense'));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Expense $expense): bool
    {
        return $user->can('view_expense') || (employeeHasPermission('view_expense'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_expense') || (employeeHasPermission('create_expense'));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Expense $expense): bool
    {
        return $user->can('update_expense') || (employeeHasPermission('update_expense'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Expense $expense): bool
    {
        return $user->can('delete_expense') || (employeeHasPermission('delete_expense'));
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_expense') || (employeeHasPermission('delete_any_expense'));
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Expense $expense): bool
    {
        return $user->can('force_delete_expense');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_expense');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Expense $expense): bool
    {
        return $user->can('restore_expense') || (employeeHasPermission('restore_expense'));
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_expense');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Expense $expense): bool
    {
        return $user->can('replicate_expense') || (employeeHasPermission('replicate_expense'));
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_expense');
    }
}
