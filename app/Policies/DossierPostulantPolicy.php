<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DossierPostulant;
use Illuminate\Auth\Access\HandlesAuthorization;

class DossierPostulantPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_dossier::postulant');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DossierPostulant $dossierPostulant): bool
    {
        return $user->can('view_dossier::postulant');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_dossier::postulant');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DossierPostulant $dossierPostulant): bool
    {
        return $user->can('update_dossier::postulant');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DossierPostulant $dossierPostulant): bool
    {
        return $user->can('delete_dossier::postulant');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_dossier::postulant');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, DossierPostulant $dossierPostulant): bool
    {
        return $user->can('force_delete_dossier::postulant');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_dossier::postulant');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, DossierPostulant $dossierPostulant): bool
    {
        return $user->can('restore_dossier::postulant');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_dossier::postulant');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, DossierPostulant $dossierPostulant): bool
    {
        return $user->can('replicate_dossier::postulant');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_dossier::postulant');
    }
}
