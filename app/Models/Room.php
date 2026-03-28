<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['room_number', 'type', 'capacity', 'price_per_night', 'status', 'floor'];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
