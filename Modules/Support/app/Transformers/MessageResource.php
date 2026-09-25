<?php

namespace Modules\Support\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'conversationId' => $this->conversation_id,
            'senderType' => $this->sender_type,
            'body' => $this->body,
            'readAt' => $this->read_at,
            'createdAt' => $this->created_at,
        ];
    }
}
