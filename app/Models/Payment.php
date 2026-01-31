<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read BelongsTo $reservation
 * @property-read BelongsTo $invoice
 * @property-read BelongsTo $processedBy
 */
class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'reservation_id',
        'invoice_id',
        'amount',
        'payment_method',
        'status',
        'transaction_id',
        'notes',
        'processed_by',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
