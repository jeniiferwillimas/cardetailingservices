<?php

namespace Modules\Booking\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Service\Models\Service;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_email',
        'customer_phone',
        'address',
        'state',
        'vehicle_info',
        'scheduled_for',
        'status',
        'notes',
        'service_id',
        'order_reference',
        'payment_status',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_for' => 'datetime',
        ];
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
