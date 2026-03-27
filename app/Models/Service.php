<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'name',
        'type',
        'price',
        'description',
        'active',
    ];

    public function charges(): HasMany
    {
        return $this->hasMany(Charge::class);
    }
}
