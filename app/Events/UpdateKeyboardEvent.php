<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UpdateKeyboardEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $keyboard;
    public $message_id;
    public $chat_id;
    public $isComplete;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($chat_id,$message_id,$keyboard)
    {
        //
        $this->chat_id = $chat_id;
        $this->message_id = $message_id;
        $this->keyboard= $keyboard;
    }


}
