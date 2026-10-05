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

    /**
     * Prepare inputs for validation: allow both JSON string or array of slots,
     * and fallback to legacy single slot format.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('slots')) {
            $slots = $this->input('slots');
            if (is_string($slots)) {
                $decoded = json_decode($slots, true);
                if (is_array($decoded)) {
                    $this->merge(['slots' => $decoded]);
                }
            }
        } elseif ($this->filled('court_id') && $this->filled('date') && $this->filled('time_slot')) {
            $this->merge([
                'slots' => [
                    [
                        'court_id' => $this->input('court_id'),
                        'date' => $this->input('date'),
                        'time_slot' => $this->input('time_slot'),
                    ],
                ],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'slots' => ['required', 'array', 'min:1'],
            'slots.*.court_id' => ['required', 'integer', 'exists:courts,id'],
            'slots.*.date' => ['required', 'date', 'after_or_equal:today'],
            'slots.*.time_slot' => ['required', 'string'],
            'payment_method' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $slots = $this->input('slots', []);
            if (! is_array($slots)) {
                return;
            }

            $seen = [];
            foreach ($slots as $index => $slot) {
                if (! is_array($slot)) {
                    continue;
                }

                $timeSlot = $slot['time_slot'] ?? '';
                $times = $this->parseSlotTimes($timeSlot);

                if ($times === null) {
                    $validator->errors()->add("slots.{$index}.time_slot", "Slot '{$timeSlot}' is not valid.");
                    continue;
                }

                [$startTime, $endTime] = $times;
                $courtId = $slot['court_id'] ?? null;
                $date = $slot['date'] ?? null;

                if (! $courtId || ! $date) {
                    continue;
                }

                $uniqueKey = "{$courtId}_{$date}_{$startTime}_{$endTime}";
                if (isset($seen[$uniqueKey])) {
                    $validator->errors()->add("slots.{$index}.time_slot", "Duplicate slot selected for the same court and time.");
                    continue;
                }
                $seen[$uniqueKey] = true;

                $conflict = Booking::where('court_id', $courtId)
                    ->where('date', $date)
                    ->where('booking_status', '!=', 'cancelled')
                    ->where(function ($query) use ($startTime, $endTime) {
                        $query->where('start_time', '<', $endTime)
                              ->where('end_time', '>', $startTime);
                    })
                    ->exists();

                if ($conflict) {
                    $validator->errors()->add("slots.{$index}.time_slot", "A selected court is already booked for {$date} at {$timeSlot}.");
                }
            }
        });
    }

    /**
     * Get all validated slots with parsed military times.
     */
    public function parsedSlots(): array
    {
        $slots = $this->input('slots', []);
        $parsed = [];

        foreach ($slots as $slot) {
            $times = $this->parseSlotTimes($slot['time_slot'] ?? '');
            if ($times !== null) {
                $parsed[] = [
                    'court_id' => (int) $slot['court_id'],
                    'date' => $slot['date'],
                    'time_slot' => $slot['time_slot'],
                    'start_time' => $times[0],
                    'end_time' => $times[1],
                ];
            }
        }

        return $parsed;
    }

    /**
     * Parse "6:00 AM - 7:00 AM" into ['06:00:00', '07:00:00'].
     */
    public function parseSlotTimes(?string $slot): ?array
    {
        if (empty($slot)) {
            return null;
        }

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

    /**
     * Legacy single slot helper.
     */
    public function parsedTimes(): ?array
    {
        $slots = $this->parsedSlots();
        if (! empty($slots)) {
            return [$slots[0]['start_time'], $slots[0]['end_time']];
        }

        return $this->parseSlotTimes($this->input('time_slot', ''));
    }
}