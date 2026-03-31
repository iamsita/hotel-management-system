<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\GuestSegmentHistory;

/**
 * @property-read HasMany $reservations
 * @property-read HasMany $bookings
 * @property-read HasMany $payments
 * @property-read HasManyThrough $foodOrders
 * @property-read HasMany $history
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'type',
        'status',
        'segment',
        'segment_metrics',
        'last_segmented_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'segment_metrics' => 'json',
            'last_segmented_at' => 'datetime',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Reservation::class, 'managed_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'processed_by');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'user_id');
    }

    public function foodOrders(): HasManyThrough
    {
        return $this->hasManyThrough(FoodOrder::class, Reservation::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(GuestSegmentHistory::class, 'user_id');
    }

    /**
     * Magic getter for accessing segment metrics as direct properties
     */
    public function __get($key)
    {
        // Access segment_metrics from attributes array to avoid recursion
        $attributes = $this->getAttributes();

        if (isset($attributes['segment_metrics'])) {
            // segment_metrics is already decoded due to JSON cast
            $metrics = $this->getAttribute('segment_metrics');

            if (is_array($metrics) && array_key_exists($key, $metrics)) {
                return $metrics[$key];
            }
        }

        return parent::__get($key);
    }
}
