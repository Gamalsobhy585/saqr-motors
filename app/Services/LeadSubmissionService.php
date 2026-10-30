<?php

namespace App\Services;

use App\Enums\EnquiryStatus;
use App\Enums\EnquiryType;
use App\Models\Enquiry;
use App\Models\Motorcycle;
use App\Models\TestRideBooking;
use Carbon\CarbonImmutable;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Validation\ValidationException;

class LeadSubmissionService
{
    /** @param array<string, mixed> $data */
    public function bookRide(Motorcycle $motorcycle, array $data): TestRideBooking
    {
        $scheduledAt = CarbonImmutable::createFromFormat(
            '!Y-m-d H:i',
            $data['ride_date'] . ' ' . $data['ride_time'],
            config('app.timezone'),
        );

        if (! $scheduledAt || ! $scheduledAt->isFuture()) {
            throw ValidationException::withMessages([
                'ride_time' => __('saqr_public.validation.future'),
            ]);
        }

        try {
            return TestRideBooking::query()->create([
                'brand_id' => $motorcycle->brand_id,
                'motorcycle_id' => $motorcycle->id,
                'location_id' => $data['ride_location_id'],
                'customer_name' => $data['ride_name'],
                'email' => $data['ride_email'],
                'phone' => $data['ride_phone'],
                'scheduled_at' => $scheduledAt,
                'notes' => $data['ride_notes'] ?? null,
            ]);
        } catch (UniqueConstraintViolationException $exception) {
            throw ValidationException::withMessages([
                'ride_time' => __('saqr_public.validation.slot_taken'),
            ]);
        }
    }

    /** @param array<string, mixed> $data */
    public function contact(Motorcycle $motorcycle, array $data): Enquiry
    {
        return Enquiry::query()->create([
            'brand_id' => $motorcycle->brand_id,
            'motorcycle_id' => $motorcycle->id,
            'name' => $data['contact_name'],
            'email' => $data['contact_email'],
            'phone' => $data['contact_phone'],
            'type' => EnquiryType::General,
            'message' => $data['contact_message'],
            'status' => EnquiryStatus::New,
        ]);
    }
}
