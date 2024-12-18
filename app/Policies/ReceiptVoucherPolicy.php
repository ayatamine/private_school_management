<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ReceiptVoucher;
use App\Models\Student;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReceiptVoucherPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_receipt::voucher') 
        || ( $user->student != null && $user->student?->termination_date == null)
        || ( $user->parent != null) 
        || (employeeHasPermission('view_any_receipt::voucher'));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ReceiptVoucher $receiptVoucher): bool
    {
        return $user->can('view_receipt::voucher') 
        || ( $user->student != null && $user->student?->termination_date == null && $receiptVoucher->student_id == $user->student?->id)
        || ( $user->parent != null && Student::findOrFail($receiptVoucher->student_id)->parent_id == $user->parent?->id) 
        || (employeeHasPermission('view_receipt::voucher'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_receipt::voucher')
        || ( $user->student != null && $user->student?->termination_date == null)
        || ( $user->parent != null) 
        || (employeeHasPermission('create_receipt::voucher'));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ReceiptVoucher $receiptVoucher): bool
    {
        return $user->can('update_receipt::voucher')  || (employeeHasPermission('update_receipt::voucher'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ReceiptVoucher $receiptVoucher): bool
    {
        return $user->can('delete_receipt::voucher') || (employeeHasPermission('delete_receipt::voucher'));
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_receipt::voucher') || (employeeHasPermission('delete_any_receipt::voucher'));
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, ReceiptVoucher $receiptVoucher): bool
    {
        return $user->can('force_delete_receipt::voucher') || (employeeHasPermission('force_delete_receipt::voucher'));
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_receipt::voucher');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, ReceiptVoucher $receiptVoucher): bool
    {
        return $user->can('restore_receipt::voucher') || (employeeHasPermission('restore_receipt::voucher'));
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_receipt::voucher');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, ReceiptVoucher $receiptVoucher): bool
    {
        return $user->can('replicate_receipt::voucher') || (employeeHasPermission('replicate_receipt::voucher'));
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_receipt::voucher');
    }
}
