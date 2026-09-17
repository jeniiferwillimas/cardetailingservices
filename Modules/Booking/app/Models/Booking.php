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

    // scheduled_for is deliberately NOT cast to 'datetime'. Customers pick a
    // wall-clock time in their own state (no timezone attached), and we want
    // that exact value stored and displayed as entered — casting to
    // Carbon/datetime would apply the app timezone and serialize with a
    // trailing 'Z', causing browsers to reinterpret and shift it when
    // displayed to an admin in a different timezone.

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
