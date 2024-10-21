<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Student;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_tuition::fee::reports');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Student $student): bool
    {
        return $user->can('view_tuition::fee::reports');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_tuition::fee::reports');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Student $student): bool
    {
        return $user->can('update_tuition::fee::reports');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Student $student): bool
    {
        return $user->can('delete_tuition::fee::reports');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_tuition::fee::reports');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Student $student): bool
    {
        return $user->can('force_delete_tuition::fee::reports');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_tuition::fee::reports');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Student $student): bool
    {
        return $user->can('restore_tuition::fee::reports');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_tuition::fee::reports');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Student $student): bool
    {
        return $user->can('replicate_tuition::fee::reports');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function approve_registeration(User $user): bool
    {
        return $user->can('approve_registeration_newest::student');
    }
    public function terminate_student_private(User $user): bool
    {
        return $user->can('terminate_student_private::student');
    }
}

