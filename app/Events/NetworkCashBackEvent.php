<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NetworkCashBackEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $cashBackValue;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userId,$cashBackValue)
    {
        //
        $this->userId = $userId;
        $this->cashBackValue = $cashBackValue;
    }


}
