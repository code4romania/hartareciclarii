<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Duplicate;
use App\Models\User;

class DuplicatePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return auth()->user()->can('view_duplicates');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Duplicate $duplicate): bool
    {
        return auth()->user()->can('view_duplicates');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return auth()->user()->can('manage_duplicates');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Duplicate $duplicate): bool
    {
        return auth()->user()->can('manage_duplicates');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Duplicate $duplicate): bool
    {
        return auth()->user()->can('manage_duplicates');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Duplicate $duplicate): bool
    {
        return auth()->user()->can('manage_duplicates');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Duplicate $duplicate): bool
    {
        return auth()->user()->can('manage_duplicates');
    }
}
