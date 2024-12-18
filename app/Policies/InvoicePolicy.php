<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Invoice;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_invoice') || (employeeHasPermission('view_any_invoice'));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Invoice $invoice): bool
    {
        return $user->can('view_invoice' || (employeeHasPermission('view_invoice')));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('{{ Create }}' || (employeeHasPermission('{{ Create }}')));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Invoice $invoice): bool
    {
        return $user->can('update_invoice' || (employeeHasPermission('update_invoice')));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->can('{{ Delete }}' || (employeeHasPermission('{{ Delete }}')));
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('{{ DeleteAny }}' || (employeeHasPermission('{{ DeleteAny }}')));
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Invoice $invoice): bool
    {
        return $user->can('{{ ForceDelete }}' || (employeeHasPermission('{{ ForceDelete }}')));
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('{{ ForceDeleteAny }}' || (employeeHasPermission('{{ ForceDeleteAny }}')));
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Invoice $invoice): bool
    {
        return $user->can('{{ Restore }}' || (employeeHasPermission('{{ Restore }}')));
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('{{ RestoreAny }}' || (employeeHasPermission('{{ RestoreAny }}')));
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Invoice $invoice): bool
    {
        return $user->can('{{ Replicate }}' || (employeeHasPermission('{{ Replicate }}')));
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('{{ Reorder }}' || (employeeHasPermission('{{ Reorder }}')));
    }
}
