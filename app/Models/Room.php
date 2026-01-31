<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = [
        'room_number',
        'room_type',
        'capacity',
        'price_per_night',
        'status',
        'housekeeping_status',
        'description',
        'floor',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function housekeeping_tasks(): HasMany
    {
        return $this->hasMany(HousekeepingTask::class);
    }

    public function current_reservation()
    {
        return $this->hasOne(Reservation::class)
            ->where('status', 'checked_in')
            ->latest();
    }
}
