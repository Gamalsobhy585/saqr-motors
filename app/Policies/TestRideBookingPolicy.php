<?php

namespace App\Policies;

use App\Models\TestRideBooking;
use App\Models\User;

class TestRideBookingPolicy
{
    public function viewAny(User $user): bool
    {
        return (bool) $user->is_active && ($user->isAdmin() || $user->isDataEntry());
    }

    public function view(User $user, TestRideBooking $booking): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, TestRideBooking $booking): bool
    {
        return false;
    }

    public function delete(User $user, TestRideBooking $booking): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, TestRideBooking $booking): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, TestRideBooking $booking): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, TestRideBooking $booking): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }
}
