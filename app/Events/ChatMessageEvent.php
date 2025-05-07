<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;

class ChatMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id;
    public $message;
    public $created_at;
    public $sender_id;
    public $receiver_id;

    /**
     * Create a new event instance.
     */
    public function __construct(array $messageData)
    {
        $this->id = $messageData['id'];
        $this->message = $messageData['message'];
        $this->created_at = $messageData['created_at'];
        $this->sender_id = $messageData['sender_id'];
        $this->receiver_id = $messageData['receiver_id'];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channelName = 'chat.' . min($this->sender_id, $this->receiver_id) . '.' . max($this->sender_id, $this->receiver_id);
        return [new PrivateChannel($channelName)];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'created_at' => $this->created_at,
        ];
    }
}
