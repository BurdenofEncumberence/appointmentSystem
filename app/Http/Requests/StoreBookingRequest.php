<?php

namespace App\Http\Requests;

use App\Models\Booking;
use App\Models\Court;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $courts = $this->input('courts', []);

        // Fallback to courts_json if courts array was not submitted or is empty
        if ((empty($courts) || !is_array($courts)) && $this->filled('courts_json')) {
            $decoded = json_decode($this->input('courts_json'), true);
            if (is_array($decoded)) {
                $courts = array_map(function ($item) {
                    return [
                        'court_id' => $item['courtId'] ?? $item['court_id'] ?? null,
                        'time_slot' => $item['timeSlot'] ?? $item['time_slot'] ?? null,
                        'date' => $item['date'] ?? null,
                    ];
                }, $decoded);
            }
        }

        // Fallback for single court legacy submission (e.g. from tests or API)
        if (empty($courts) && $this->has('court_id') && $this->has('time_slot')) {
            $courts = [[
                'court_id' => $this->input('court_id'),
                'time_slot' => $this->input('time_slot'),
                'date' => $this->input('date'),
            ]];
        }

        // If courts is not an array, make it an empty array
        if (!is_array($courts)) {
            $courts = [];
        }

        // Filter and sanitize court entries
        $filteredCourts = [];
        foreach ($courts as $courtData) {
            if (is_array($courtData) && isset($courtData['court_id']) && isset($courtData['time_slot'])) {
                if (!empty($courtData['court_id']) && !empty($courtData['time_slot'])) {
                    $courtData['court_id'] = (int) $courtData['court_id'];
                    $courtData['date'] = $courtData['date'] ?? $this->input('date');
                    $filteredCourts[] = $courtData;
                }
            }
        }

        $mergeData = [
            'courts' => array_values($filteredCourts),
        ];

        if (!$this->has('payment_method')) {
            $mergeData['payment_method'] = 'online';
        }

        $this->merge($mergeData);
    }

    public function rules(): array
    {
        return [
            'courts' => ['required', 'array', 'min:1'],
            'courts.*.court_id' => ['required', 'integer', 'exists:courts,id'],
            'courts.*.time_slot' => ['required', 'string'],
            'courts.*.date' => ['required', 'date', 'after_or_equal:today'],
            'date' => ['nullable', 'date', 'after_or_equal:today'],
            'event_id' => ['nullable', 'integer', 'exists:events,id'],
            'payment_method' => ['required', 'string', 'in:gcash,card,cash,online'],
        ];
    }

    public function messages(): array
    {
        return [
            'courts.required' => 'Please select at least one court.',
            'courts.min' => 'Please select at least one court.',
            'courts.*.court_id.required' => 'Court selection is required.',
            'courts.*.time_slot.required' => 'Time slot is required.',
            'courts.*.date.required' => 'Date is required for each court booking.',
            'courts.*.date.after_or_equal' => 'Booking date cannot be in the past.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $courts = $this->input('courts', []);
            $seen = [];

            foreach ($courts as $index => $courtData) {
                $times = $this->parsedTimes($courtData['time_slot']);

                if ($times === null) {
                    $validator->errors()->add("courts.{$index}.time_slot", 'That time slot is not valid.');
                    continue;
                }

                [$startTime, $endTime] = $times;
                $date = $courtData['date'] ?? $this->input('date');

                if (!$date) {
                    $validator->errors()->add("courts.{$index}.date", 'A booking date is required.');
                    continue;
                }

                // Check conflict within the same submission
                $slotKey = "{$courtData['court_id']}_{$date}_{$startTime}_{$endTime}";
                if (isset($seen[$slotKey])) {
                    $validator->errors()->add("courts.{$index}.time_slot", 'You have selected the same court and time slot more than once.');
                    continue;
                }
                $seen[$slotKey] = true;

                $conflict = Booking::where('court_id', $courtData['court_id'])
                    ->where('date', $date)
                    ->where('booking_status', '!=', 'cancelled')
                    ->where(function ($query) use ($startTime, $endTime) {
                        $query->where('start_time', '<', $endTime)
                              ->where('end_time', '>', $startTime);
                    })
                    ->exists();

                if ($conflict) {
                    $courtName = Court::find($courtData['court_id'])?->court_name ?? "Court {$courtData['court_id']}";
                    $validator->errors()->add("courts.{$index}.time_slot", "{$courtName} is already booked for {$courtData['time_slot']} on {$date}.");
                }
            }
        });
    }

    /**
     * Parse the "6:00 AM - 7:00 AM" style string sent from the booking page
     * into ['06:00:00', '07:00:00'], or null if it can't be parsed safely.
     */
    public function parsedTimes(?string $timeSlot = null): ?array
    {
        $slot = $timeSlot ?? $this->input('time_slot', '');
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