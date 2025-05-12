<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ChatMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id;
    public $message;
    public $created_at;
    public $sender_id;
    public $sender_type;
    public $receiver_id;
    public $receiver_type;
    public $is_super_admin;

    /**
     * Create a new event instance.
     */
    public function __construct($data)
    {
        Log::info('ChatMessageEvent constructor called with data:', $data);
        $this->id = $data['id'];
        $this->message = $data['message'];
        $this->created_at = $data['created_at'];
        $this->sender_id = $data['sender_id'];
        $this->sender_type = $data['sender_type'];
        $this->receiver_id = $data['receiver_id'];
        $this->receiver_type = $data['receiver_type'];
        $this->is_super_admin = $data['is_super_admin'] ?? false;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        Log::info('broadcastOn method called');
        $channelName = 'chat.' . min($this->sender_id, $this->receiver_id) . '.' . max($this->sender_id, $this->receiver_id);
        Log::info('Broadcasting on channel:', ['channel' => $channelName]);
        return [new Channel($channelName)];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        $data = [
            'id' => $this->id,
            'message' => $this->message,
            'created_at' => $this->created_at,
            'sender_id' => $this->sender_id,
            'sender_type' => $this->sender_type,
            'receiver_id' => $this->receiver_id,
            'receiver_type' => $this->receiver_type,
            'is_super_admin' => $this->is_super_admin,
        ];
        Log::info('Broadcasting data:', $data);
        return $data;
    }
}
