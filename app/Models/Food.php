<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    protected $table = 'foods';

    protected $fillable = [
        'name',
        'category',
        'price',
        'description',
        'available',
        'image_url',
    ];

    public function orders()
    {
        return $this->hasMany(FoodOrder::class);
    }
}
