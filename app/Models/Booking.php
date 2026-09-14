<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
public function user()
{
    return $this->belongsTo(User::class);
}

public function court()
{
    return $this->belongsTo(Court::class);
}

public function event()
{
    return $this->belongsTo(Event::class);
}

public function payments()
{
    return $this->hasMany(Payment::class);
}

}
