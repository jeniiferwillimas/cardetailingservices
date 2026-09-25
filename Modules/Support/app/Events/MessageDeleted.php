<?php

namespace Modules\Support\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class MessageDeleted implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $conversationUuid,
        public int $messageId,
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel('conversation.'.$this->conversationUuid);
    }

    public function broadcastAs(): string
    {
        return 'message.deleted';
    }

    public function broadcastWith(): array
    {
        return ['id' => $this->messageId];
    }
}
