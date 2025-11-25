<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PrivateMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public $senderId;    // sender's ID

    public $receiverId;

    public function __construct($message, $senderId, $receiverId)
    {
        $this->message = $message;
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
    }

    public function broadcastOn()
    {
        // Log::info('Broadcasting message to user '.$this->receiverId, ['message' => $this->message]);

        // Private channel specific to receiver
        return new PrivateChannel('chat.'.$this->receiverId);
    }

    public function broadcastAs()
    {
        return 'private.message.sent';
    }

    public function broadcastWith()
    {
        // this is the payload JS will receive
        return [
            'message' => $this->message,
            'sender_id' => $this->senderId,
            'receiverId' => $this->receiverId,
        ];
    }
}
