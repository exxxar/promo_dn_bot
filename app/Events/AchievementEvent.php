<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AchievementEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $trigger_type;
    public $trigger_value;
    public $user;
    public $isComplete;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($triggerType,$triggerValue,$user)
    {
        $this->user = $user;
        $this->trigger_type = $triggerType;
        $this->trigger_value = $triggerValue;

        $this->isComplete = false;
    }

}
