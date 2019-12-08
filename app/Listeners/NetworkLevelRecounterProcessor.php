<?php

namespace App\Listeners;

use App\Events\NetworkLevelRecounterEvent;
use App\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NetworkLevelRecounterProcessor
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  object $event
     * @return void
     */
    public function handle(NetworkLevelRecounterEvent $event)
    {
        $userList = User::where("activated", "1")->get();

        foreach ($userList as $u) {
            $time_1 = date_timestamp_get(new DateTime($u->updated));
            $time_2 = $time_1 + 720 * 60 * 60 * 1000;

            $time_3 = date_timestamp_get(now());
            if ($time_3 > $time_2) {
                $u->activated = 0;
                $u->save();
            }
        }

        $user = User::with(["childs"])
            ->where("id", $event->userId)
            ->first();

        $sLevel1 = $sLevel2 = $sLevel3 = 0;

        foreach ($user->childs as $child1) {
            $sLevel1 += $child1->activated == 1 ? 1 : 0;
            if ($sLevel1 >= 150) {
                $childs2 = $child1->childs;
                foreach ($childs2 as $child2) {
                    $sLevel2 += $child2->activated == 1 ? 1 : 0;

                    $childs3 = $child2->childs;
                    foreach ($childs3 as $child3)
                        $sLevel3 += $child3->activated == 1 ? 1 : 0;


                }
            }
        }

        $networkFriendsSummary = $sLevel1 + $sLevel2 + $sLevel3;

        if ($networkFriendsSummary < 150)
            $user->current_network_level = 0;

        if ($networkFriendsSummary >= 150)
            $user->current_network_level = 1;

        if ($networkFriendsSummary >= 2000)
            $user->current_network_level = 2;

        if ($networkFriendsSummary >= 10000)
            $user->current_network_level = 3;

        $user->network_friends_count = $networkFriendsSummary;
        $user->updated_at = Carbon::now();
        $user->save();
    }
}
