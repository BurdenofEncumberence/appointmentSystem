<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_title',
        'start_date',
        'end_date',
        'discount',
        'details',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}