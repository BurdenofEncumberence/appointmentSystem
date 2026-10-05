<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Carbon;

class BookingPolicy
{
    /**
     * Determine whether the user can view any bookings.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the specific booking.
     */
    public function view(User $user, Booking $booking): bool
    {
        if ($user->isAdmin() || $user->hasRole('manager')) {
            return true;
        }

        if ($user->hasRole('staff')) {
            return Carbon::parse($booking->date)->isSameDay(Carbon::today());
        }

        return $booking->user_id === $user->id;
    }

    /**
     * Determine whether the user can create bookings (players only).
     */
    public function create(User $user): bool
    {
        return $user->isPlayer();
    }

    /**
     * Determine whether the user can update attendance/status.
     */
    public function updateStatus(User $user, Booking $booking): bool
    {
        return $user->hasRole('staff') || $user->isAdmin() || $user->hasRole('manager');
    }
}
