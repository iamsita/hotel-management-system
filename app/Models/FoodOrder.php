<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }
}
