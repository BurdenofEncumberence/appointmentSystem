<?php

namespace App\Http\Requests;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'court_id' => ['required', 'integer', 'exists:courts,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'time_slot' => ['required', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $times = $this->parsedTimes();

            if ($times === null) {
                $validator->errors()->add('time_slot', 'That time slot is not valid.');
                return;
            }

            [$startTime, $endTime] = $times;

            $conflict = Booking::where('court_id', $this->input('court_id'))
                ->where('date', $this->input('date'))
                ->where('booking_status', '!=', 'cancelled')
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->where('start_time', '<', $endTime)
                          ->where('end_time', '>', $startTime);
                })
                ->exists();

            if ($conflict) {
                $validator->errors()->add('time_slot', 'That court is already booked for this time.');
            }
        });
    }

    /**
     * Parse the "6:00 AM - 7:00 AM" style string sent from the booking page
     * into ['06:00:00', '07:00:00'], or null if it can't be parsed safely.
     */
    public function parsedTimes(): ?array
    {
        $slot = $this->input('time_slot', '');
        $parts = array_map('trim', explode('-', $slot));

        if (count($parts) !== 2) {
            return null;
        }

        try {
            $start = Carbon::parse($parts[0])->format('H:i:s');
            $end = Carbon::parse($parts[1])->format('H:i:s');
        } catch (\Exception $e) {
            return null;
        }

        if ($start >= $end) {
            return null;
        }

        return [$start, $end];
    }
}