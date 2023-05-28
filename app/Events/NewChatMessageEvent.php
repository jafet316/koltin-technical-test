<?php

namespace App\Events;

use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The Chat instance
     * 
     * @var \App\Models\Chat
     */
    public $chat;

    /**
     * The message
     * 
     * @var \App\Models\Message
     */
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Chat $chat, Message $message)
    {
        $this->chat     = $chat;
        $this->message  = $message;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return ['message' => new MessageResource($this->message)];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): Channel
    {
        return new PrivateChannel('chats.' . $this->chat->id);
    }
}
