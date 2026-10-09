<?php

namespace App\Http\Requests;

use App\Models\Booking;
use App\Models\Court;
use App\Models\OpenPlaySession;
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
     * Prepare inputs for validation: allow JSON strings or arrays from 'slots' or 'courts',
     * and fallback to single slot legacy submission.
     */
    protected function prepareForValidation(): void
    {
        $rawSlots = [];

        if ($this->has('slots')) {
            $slotsInput = $this->input('slots');
            if (is_string($slotsInput)) {
                $decoded = json_decode($slotsInput, true);
                if (is_array($decoded)) {
                    $rawSlots = $decoded;
                }
            } elseif (is_array($slotsInput)) {
                $rawSlots = $slotsInput;
            }
        } elseif ($this->has('courts')) {
            $courtsInput = $this->input('courts');
            if (is_array($courtsInput)) {
                $rawSlots = array_map(function ($item) {
                    return [
                        'court_id' => $item['court_id'] ?? $item['courtId'] ?? null,
                        'time_slot' => $item['time_slot'] ?? $item['timeSlot'] ?? null,
                        'date' => $item['date'] ?? $this->input('date'),
                    ];
                }, $courtsInput);
            }
        } elseif ($this->filled('courts_json')) {
            $decoded = json_decode($this->input('courts_json'), true);
            if (is_array($decoded)) {
                $rawSlots = array_map(function ($item) {
                    return [
                        'court_id' => $item['court_id'] ?? $item['courtId'] ?? null,
                        'time_slot' => $item['time_slot'] ?? $item['timeSlot'] ?? null,
                        'date' => $item['date'] ?? $this->input('date'),
                    ];
                }, $decoded);
            }
        } elseif ($this->filled('court_id') && $this->filled('time_slot')) {
            $rawSlots = [
                [
                    'court_id' => $this->input('court_id'),
                    'date' => $this->input('date', today()->toDateString()),
                    'time_slot' => $this->input('time_slot'),
                ],
            ];
        }

        // Clean & normalize items
        $normalized = [];
        foreach ($rawSlots as $item) {
            if (is_array($item) && !empty($item['court_id']) && !empty($item['time_slot'])) {
                $normalized[] = [
                    'court_id' => (int) $item['court_id'],
                    'date' => $item['date'] ?? $this->input('date'),
                    'time_slot' => $item['time_slot'],
                ];
            }
        }

        $mergeData = [
            'slots' => $normalized,
            'courts' => $normalized,
        ];

        if (!$this->has('payment_method')) {
            $mergeData['payment_method'] = 'online';
        }

        $this->merge($mergeData);
    }

    public function rules(): array
    {
        return [
            'slots' => ['required', 'array', 'min:1'],
            'slots.*.court_id' => ['required', 'integer', 'exists:courts,id'],
            'slots.*.date' => ['required', 'date', 'after_or_equal:today'],
            'slots.*.time_slot' => ['required', 'string'],
            'event_id' => ['nullable', 'integer', 'exists:events,id'],
            'payment_method' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'slots.required' => 'Please select at least one court.',
            'slots.min' => 'Please select at least one court.',
            'slots.*.court_id.required' => 'Court selection is required.',
            'slots.*.time_slot.required' => 'Time slot is required.',
            'slots.*.date.required' => 'Date is required for each court booking.',
            'slots.*.date.after_or_equal' => 'Booking date cannot be in the past.',
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
                    $courtName = Court::find($courtId)?->court_name ?? "Court {$courtId}";
                    $validator->errors()->add("slots.{$index}.time_slot", "{$courtName} is already booked for {$date} at {$timeSlot}.");
                    continue;
                }

                $openPlayConflict = OpenPlaySession::whereDate('date', $date)
                    ->whereIn('session_status', ['scheduled', 'approved_pending_payment', 'ongoing'])
                    ->whereHas('courts', function ($q) use ($courtId) {
                        $q->where('courts.id', $courtId);
                    })
                    ->where(function ($query) use ($startTime, $endTime) {
                        $query->where('start_time', '<', $endTime)
                              ->where('end_time', '>', $startTime);
                    })
                    ->first();

                if ($openPlayConflict) {
                    $courtName = Court::find($courtId)?->court_name ?? "Court {$courtId}";
                    $validator->errors()->add("slots.{$index}.time_slot", "{$courtName} is reserved for {$openPlayConflict->title} ({$openPlayConflict->time_window}) on {$date}.");
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
     * Parse times helper, supporting optional string parameter or fallback to inputs.
     */
    public function parsedTimes(?string $timeSlot = null): ?array
    {
        if ($timeSlot !== null) {
            return $this->parseSlotTimes($timeSlot);
        }

        $slots = $this->parsedSlots();
        if (! empty($slots)) {
            return [$slots[0]['start_time'], $slots[0]['end_time']];
        }

        return $this->parseSlotTimes($this->input('time_slot', ''));
    }
}