<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_employee') || (employeeHasPermission('view_any_employee'));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Employee $employee): bool
    {
        return $user->can('view_employee') || (employeeHasPermission('view_employee'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_employee') || (employeeHasPermission('create_employee'));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Employee $employee): bool
    {
        return $user->can('update_employee') || (employeeHasPermission('update_employee'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Employee $employee): bool
    {
        return $user->can('delete_employee') || (employeeHasPermission('delete_employee'));
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_employee') || (employeeHasPermission('delete_any_employee'));
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Employee $employee): bool
    {
        return $user->can('{{ ForceDelete }}');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('{{ ForceDeleteAny }}');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Employee $employee): bool
    {
        return $user->can('{{ Restore }}') ;
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('{{ RestoreAny }}') ;
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Employee $employee): bool
    {
        return $user->can('{{ Replicate }}');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('{{ Reorder }}');
    }
    public function approve_employee_registeration(User $user): bool
    {
        return $user->can('approve_employee_registeration_employee') || (employeeHasPermission('approve_employee_registeration_employee'));
    }
    public function finish_employee_duration(User $user): bool
    {
        return $user->can('finish_employee_duration_employee') || (employeeHasPermission('finish_employee_duration_employee'));
    }
}
