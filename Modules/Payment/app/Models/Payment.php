<?php

namespace Modules\Payment\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Booking\Models\Booking;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_reference',
        'idempotency_key',
        'amount',
        'currency',
        'status',
        'provider',
        'provider_invoice_id',
        'provider_payment_id',
        'raw_response',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'raw_response' => 'array',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'order_reference', 'order_reference');
    }
}
