<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LocationMachine;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocationMachinePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_location::machine');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LocationMachine $locationMachine): bool
    {
        return $user->can('view_location::machine');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_location::machine');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LocationMachine $locationMachine): bool
    {
        return $user->can('update_location::machine');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LocationMachine $locationMachine): bool
    {
        return $user->can('delete_location::machine');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_location::machine');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, LocationMachine $locationMachine): bool
    {
        return $user->can('force_delete_location::machine');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_location::machine');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, LocationMachine $locationMachine): bool
    {
        return $user->can('restore_location::machine');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_location::machine');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, LocationMachine $locationMachine): bool
    {
        return $user->can('replicate_location::machine');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_location::machine');
    }
}
