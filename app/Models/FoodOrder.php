<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read BelongsTo $food
 * @property-read BelongsTo $reservation
 */
class FoodOrder extends Model
{
    protected $table = 'food_orders';

    protected $fillable = [
        'reservation_id',
        'food_id',
        'quantity',
        'price',
        'special_notes',
        'status',
        'ordered_at',
        'delivered_at',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function food(): BelongsTo
    {
        return $this->belongsTo(Food::class);
    }
}
