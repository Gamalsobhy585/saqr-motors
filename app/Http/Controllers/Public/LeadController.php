<?php

namespace App\Http\Controllers\Public;

use App\Enums\MotorcycleAvailability;
use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreContactRequest;
use App\Http\Requests\Public\StoreRideRequest;
use App\Models\Motorcycle;
use App\Services\LeadSubmissionService;

class LeadController extends Controller
{
    public function book(StoreRideRequest $request, Motorcycle $motorcycle, LeadSubmissionService $submissions)
    {
        abort_unless(
            $motorcycle->is_published && $motorcycle->brand?->is_active
                && $motorcycle->availability_status === MotorcycleAvailability::Available,
            404
        );

        $submissions->bookRide($motorcycle, $request->validated());

        return back()->with('success', __('saqr_public.messages.booking_received'));
    }

    public function contact(StoreContactRequest $request, Motorcycle $motorcycle, LeadSubmissionService $submissions)
    {
        abort_unless($motorcycle->is_published && $motorcycle->brand?->is_active, 404);

        $submissions->contact($motorcycle, $request->validated());

        return back()->with('success', __('saqr_public.messages.contact_received'));
    }
}
