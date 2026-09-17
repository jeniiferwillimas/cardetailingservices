<?php

namespace Modules\Support\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'customerName' => $this->customer_name,
            'customerEmail' => $this->customer_email,
            'status' => $this->status,
            'lastMessageAt' => $this->last_message_at,
            'unreadCount' => $this->when(
                $this->relationLoaded('messages') || isset($this->unread_count),
                fn () => $this->unread_count ?? $this->messages->whereNull('read_at')->where('sender_type', 'customer')->count(),
            ),
            'createdAt' => $this->created_at,
        ];
    }
}
