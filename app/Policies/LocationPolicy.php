<?php

namespace App\Policies;

use App\Models\Location;
use App\Models\User;

class LocationPolicy
{
    public function viewAny(User $user): bool
    {
        return (bool) $user->is_active && ($user->isAdmin() || $user->isDataEntry());
    }

    public function view(User $user, Location $location): bool
    {
        return false; // No View page.
    }

    public function create(User $user): bool
    {
        return (bool) $user->is_active && $user->isAdmin();
    }

    public function update(User $user, Location $location): bool
    {
        return (bool) $user->is_active && $user->isAdmin();
    }

    public function delete(User $user, Location $location): bool
    {
        return (bool) $user->is_active && $user->isAdmin();
    }

    public function deleteAny(User $user): bool { return false; }
    public function restore(User $user, Location $location): bool { return false; }
    public function restoreAny(User $user): bool { return false; }
    public function forceDelete(User $user, Location $location): bool { return false; }
    public function forceDeleteAny(User $user): bool { return false; }
    public function replicate(User $user, Location $location): bool { return false; }
    public function reorder(User $user): bool { return false; }
}
