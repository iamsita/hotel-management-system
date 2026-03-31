<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuestSegmentHistory extends Model
{
    use HasFactory;

    protected $table = 'guest_segment_histories';

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
