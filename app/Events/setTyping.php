<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class setTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $conversationId;
    public $isTyping;

    /**
     * Create a new event instance.
     */
    public function __construct($conversationId, $isTyping)
    {
        $this->conversationId = $conversationId;
        $this->isTyping = $isTyping;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('typingState'),
        ];
    }
}
