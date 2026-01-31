<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class GuestAuth extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'guests';

    protected $guard = 'guest';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'id_number',
        'id_type',
        'address',
        'city',
        'country',
        'guest_type',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function foodOrders()
    {
        return $this->hasManyThrough(FoodOrder::class, Reservation::class);
    }
}
