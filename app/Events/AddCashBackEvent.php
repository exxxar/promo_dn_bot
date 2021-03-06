<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use phpDocumentor\Reflection\Types\Integer;

class AddCashBackEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    public $company_id;
    public $money_in_check;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(int $user_id, int $company_id, int $money_in_check)
    {
        //
        $this->user_id = $user_id;
        $this->company_id = $company_id;
        $this->money_in_check = $money_in_check;

    }

}
