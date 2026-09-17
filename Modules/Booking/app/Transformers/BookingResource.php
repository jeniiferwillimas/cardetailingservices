<?php

namespace Modules\Booking\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customerName' => $this->customer_name,
            'customerEmail' => $this->customer_email,
            'customerPhone' => $this->customer_phone,
            'address' => $this->address,
            'state' => $this->state,
            'vehicleInfo' => $this->vehicle_info,
            'scheduledFor' => $this->scheduled_for,
            'status' => $this->status,
            'notes' => $this->notes,
            'orderReference' => $this->order_reference,
            'paymentStatus' => $this->payment_status,
            'createdAt' => $this->created_at,
            'service' => [
                'id' => $this->service->id,
                'name' => $this->service->name,
                'price' => (float) $this->service->price,
            ],
        ];
    }
}
