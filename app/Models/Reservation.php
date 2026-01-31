<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'check_in_date',
        'check_out_date',
        'number_of_guests',
        'status',
        'total_amount',
        'special_requests',
        'managed_by',
    ];

    protected $casts = [
        'check_in_date' => 'datetime',
        'check_out_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function charges(): HasMany
    {
        return $this->hasMany(Charge::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function foodOrders()
    {
        return $this->hasMany(FoodOrder::class);
    }

    public function cleaningRequests()
    {
        return $this->hasMany(CleaningRequest::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function managedByUser()
    {
        return $this->belongsTo(User::class, 'managed_by');
    }

    public function getNumberOfNightsAttribute()
    {
        return $this->check_out_date->diffInDays($this->check_in_date);
    }

    public function getTotalChargesAttribute()
    {
        return $this->charges()->where('status', '!=', 'cancelled')->sum('amount');
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled')->where('status', '!=', 'checked_out');
    }
}
