<?php

namespace App\Policies;

use App\Models\Enquiry;
use App\Models\User;

class EnquiryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active && ($user->isAdmin() || $user->isDataEntry());
    }

    public function view(User $user, Enquiry $enquiry): bool
    {
        return $this->canHandle($user, $enquiry);
    }

    public function create(User $user): bool
    {
        // The public website creates enquiries, not the admin panel.
        return false;
    }

    public function update(User $user, Enquiry $enquiry): bool
    {
        return $this->canHandle($user, $enquiry);
    }

    public function delete(User $user, Enquiry $enquiry): bool
    {
        return $user->is_active && $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, Enquiry $enquiry): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, Enquiry $enquiry): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, Enquiry $enquiry): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }

    private function canHandle(User $user, Enquiry $enquiry): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        // A general enquiry with no brand is visible to admins only.
        return $user->isDataEntry()
            && $enquiry->brand_id !== null
            && $user->brands()->whereKey($enquiry->brand_id)->exists();
    }
}
