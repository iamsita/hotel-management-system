<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestSegment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'segment',
        'metrics',
    ];

    protected function casts(): array
    {
        return [
            'metrics' => 'json',
        ];
    }
}
