<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ropa;
use Illuminate\Auth\Access\HandlesAuthorization;

class RopaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->can('view_any_ropa');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ropa  $ropa
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Ropa $ropa)
    {
        return $user->can('view_ropa');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->can('create_ropa');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ropa  $ropa
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Ropa $ropa)
    {
        return $user->can('update_ropa');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ropa  $ropa
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Ropa $ropa)
    {
        return $user->can('delete_ropa');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function deleteAny(User $user)
    {
        return $user->can('delete_any_ropa');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ropa  $ropa
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Ropa $ropa)
    {
        return $user->can('force_delete_ropa');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDeleteAny(User $user)
    {
        return $user->can('force_delete_any_ropa');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ropa  $ropa
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Ropa $ropa)
    {
        return $user->can('restore_ropa');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restoreAny(User $user)
    {
        return $user->can('restore_any_ropa');
    }

    /**
     * Determine whether the user can replicate.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ropa  $ropa
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function replicate(User $user, Ropa $ropa)
    {
        return $user->can('replicate_ropa');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function reorder(User $user)
    {
        return $user->can('reorder_ropa');
    }

}
