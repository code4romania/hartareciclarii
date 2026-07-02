<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Contribution;
use App\Models\User;

class ContributionPolicy
{
    public function viewAny(User $user): bool
    {
        return auth()->user()->can('view_admin_users');
    }

    public function view(User $user, Contribution $contribution): bool
    {
        return auth()->user()->can('view_admin_users');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Contribution $contribution): bool
    {
        return false;
    }

    public function delete(User $user, Contribution $contribution): bool
    {
        return false;
    }

    public function restore(User $user, Contribution $contribution): bool
    {
        return false;
    }

    public function forceDelete(User $user, Contribution $contribution): bool
    {
        return false;
    }
}
