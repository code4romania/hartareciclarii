<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ImportExport;
use App\Models\User;

class ImportExportPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return auth()->user()->can('view_imports');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ImportExport $importExport): bool
    {
        return auth()->user()->can('view_imports');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return auth()->user()->can('manage_imports');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ImportExport $importExport): bool
    {
        return auth()->user()->can('manage_imports');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ImportExport $importExport): bool
    {
        return auth()->user()->can('manage_imports');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ImportExport $importExport): bool
    {
        return auth()->user()->can('manage_imports');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ImportExport $importExport): bool
    {
        return auth()->user()->can('manage_imports');
    }
}
