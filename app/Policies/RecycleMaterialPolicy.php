<?php

namespace App\Policies;

use App\Models\RecycleMaterial;
use App\Models\User;

class RecycleMaterialPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return auth()->user()->can('view_recycle_materials');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RecycleMaterial $recycleMaterial): bool
    {
        return auth()->user()->can('view_recycle_materials');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return auth()->user()->can('manage_recycle_materials');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RecycleMaterial $recycleMaterial): bool
    {
        return auth()->user()->can('manage_recycle_materials');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RecycleMaterial $recycleMaterial): bool
    {
        return auth()->user()->can('manage_recycle_materials');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RecycleMaterial $recycleMaterial): bool
    {
        return auth()->user()->can('manage_recycle_materials');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RecycleMaterial $recycleMaterial): bool
    {
        return auth()->user()->can('manage_recycle_materials');
    }
}
