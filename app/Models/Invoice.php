<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'reservation_id',
        'subtotal',
        'tax',
        'discount',
        'total_amount',
        'status',
        'issue_date',
        'due_date',
        'paid_date',
        'payment_method',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public static function generateInvoiceNumber()
    {
        $lastInvoice = self::latest()->first();
        $number = $lastInvoice ? intval(substr($lastInvoice->invoice_number, 3)) + 1 : 1;

        return 'INV'.str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
