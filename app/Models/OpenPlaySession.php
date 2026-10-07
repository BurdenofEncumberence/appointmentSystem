<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class OpenPlaySession extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'session_type',
        'date',
        'start_time',
        'end_time',
        'max_capacity',
        'skill_level',
        'price_per_slot',
        'details',
        'session_status',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'price_per_slot' => 'decimal:2',
        'max_capacity' => 'integer',
    ];

    public function courts(): BelongsToMany
    {
        return $this->belongsToMany(Court::class, 'open_play_session_courts');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(OpenPlayRegistration::class);
    }

    public function activeRegistrations(): HasMany
    {
        return $this->hasMany(OpenPlayRegistration::class)
            ->whereIn('payment_status', ['paid', 'pending']);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Total registered player slots (excluding cancelled).
     */
    public function getRegisteredSlotsCountAttribute(): int
    {
        return (int) $this->activeRegistrations()->sum('slots_count');
    }

    /**
     * Remaining slots available in real-time.
     */
    public function getRemainingSlotsAttribute(): int
    {
        return max(0, $this->max_capacity - $this->registered_slots_count);
    }

    /**
     * Capacity percentage filled (0-100).
     */
    public function getCapacityPercentAttribute(): float
    {
        if ($this->max_capacity <= 0) {
            return 0.0;
        }

        return min(round(($this->registered_slots_count / $this->max_capacity) * 100, 1), 100.0);
    }

    /**
     * Check if session is full.
     */
    public function getIsFullAttribute(): bool
    {
        return $this->remaining_slots <= 0;
    }

    /**
     * Formatted time window label (e.g., "6:00 PM – 9:00 PM").
     */
    public function getTimeWindowAttribute(): string
    {
        $start = Carbon::parse($this->start_time)->format('g:i A');
        $end = Carbon::parse($this->end_time)->format('g:i A');

        return "{$start} – {$end}";
    }

    /**
     * Formatted duration in hours (e.g. "3.0 hours").
     */
    public function getDurationHoursAttribute(): float
    {
        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);
        $mins = abs($start->diffInMinutes($end));

        return $mins > 0 ? round($mins / 60, 1) : 1.0;
    }

    /**
     * Comma-separated list of allocated court names.
     */
    public function getAllocatedCourtsLabelAttribute(): string
    {
        return $this->courts->pluck('court_name')->join(', ');
    }
}
