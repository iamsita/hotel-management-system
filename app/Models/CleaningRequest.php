<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CleaningRequest extends Model
{
    protected $table = 'cleaning_requests';

    protected $fillable = [
        'reservation_id',
        'room_id',
        'request_type',
        'description',
        'priority',
        'status',
        'requested_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'requested_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
