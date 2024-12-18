<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Course;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoursePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_course') || (employeeHasPermission('view_any_course'));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Course $course): bool
    {
        return $user->can('view_course') || (employeeHasPermission('view_course'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_course') || (employeeHasPermission('create_course'));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Course $course): bool
    {
        return $user->can('update_course') || (employeeHasPermission('update_course'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Course $course): bool
    {
        return $user->can('delete_course') || (employeeHasPermission('delete_course'));
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_course') || (employeeHasPermission('delete_any_course'));
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Course $course): bool
    {
        return $user->can('force_delete_course') || (employeeHasPermission('force_delete_course'));
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_course');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Course $course): bool
    {
        return $user->can('restore_course') || (employeeHasPermission('restore_course'));
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_course');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Course $course): bool
    {
        return $user->can('replicate_course') || (employeeHasPermission('replicate_course'));
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_course');
    }
}
