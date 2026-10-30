<?php

namespace App\Http\Requests\Public;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreRideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ride_name' => ['required', 'string', 'min:2', 'max:120'],
            'ride_email' => ['required', 'email:rfc', 'max:255'],
            'ride_phone' => ['required', 'regex:/^(?:\+20|0)?1[0125][0-9]{8}$/'],
            'ride_location_id' => ['required', 'integer', 'exists:locations,id'],
            'ride_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'ride_time' => ['required', Rule::in(config('saqr_public.ride_slots', []))],
            'ride_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }
            $slot = CarbonImmutable::createFromFormat('!Y-m-d H:i',
                $this->input('ride_date') . ' ' . $this->input('ride_time'),
                config('app.timezone'));
            if (! $slot || ! $slot->isFuture()) {
                $validator->errors()->add('ride_time', __('saqr_public.validation.future'));
            }
        });
    }
}
