<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DepenseCamion;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepenseCamionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_depense::camion');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DepenseCamion $depenseCamion): bool
    {
        return $user->can('view_depense::camion');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_depense::camion');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DepenseCamion $depenseCamion): bool
    {
        return $user->can('update_depense::camion');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DepenseCamion $depenseCamion): bool
    {
        return $user->can('delete_depense::camion');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_depense::camion');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, DepenseCamion $depenseCamion): bool
    {
        return $user->can('force_delete_depense::camion');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_depense::camion');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, DepenseCamion $depenseCamion): bool
    {
        return $user->can('restore_depense::camion');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_depense::camion');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, DepenseCamion $depenseCamion): bool
    {
        return $user->can('replicate_depense::camion');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_depense::camion');
    }
}
