<?php

namespace App\Listeners;

use App\Events\ActivateUserEvent;
use App\RefferalsHistory;
use App\User;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

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

        try {
            $ref = RefferalsHistory::where("user_recipient_id", $userEvent->user->id)->first();
            $ref->activated = 1;
            $ref->save();

            $user = User::find($userEvent->user->id);

            $user->activated = 1;
            $user->updated_at = Carbon::now();
            $user->save();
        }catch (\Exception $e){
            Log::info($e->getMessage()." ".$e->getLine());
        }


    }
}
