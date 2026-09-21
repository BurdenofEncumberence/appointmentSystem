<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
    use HasFactory;

    protected $fillable = [
        'court_name',
        'size',
        'price_per_hour',
        'court_status',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}