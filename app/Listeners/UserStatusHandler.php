<?php

namespace App\Listeners;

use App\Events\ActivateUserEvent;
use App\RefferalsHistory;
use App\User;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserStatusHandler
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(ActivateUserEvent $userEvent)
    {
        //

        $ref = RefferalsHistory::where("user_recipient_id", $userEvent->user->id);
        $ref->activated = 1;
        $ref->save();


        $userEvent->user->activated = 1;
        $userEvent->user->updated_at = Carbon::now();
        $userEvent->user->save();


    }
}
