<?php

namespace App\Listeners;

use App\Events\NetworkCashBackEvent;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class NetworkCashBackProcessor
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
    public function handle(NetworkCashBackEvent $event)
    {
        $user = User::with(["parent"])
            ->where('id', $event->userId)
            ->first();

        for ($level_index = 1; $level_index <= 3; $level_index++) {

            switch ($level_index) {
                case 1:
                    $parent = $user->parent ?? null;
                    break;
                case 2:
                    $parent = $user->parent != null ? ($user->parent()->with(["parent"])->first())->parent : null;
                    break;
                case 3:

                    $parent = $user->parent != null ?
                        ($user->parent()->with(["parent"])->first())->parent != null ?
                            (($user->parent()->with(["parent"])->first())->parent()->with(["parent"])->first())->parent : null
                        : null;
                    break;
            }


            if ($parent != null) {

                if ($parent->current_network_level >= $level_index &&
                    $parent->current_network_level > 0 &&
                    $parent->activated == 1) {
                    $networkCashBackValue = ($event->cashBackValue * env('CASHBACK_NETWORK_PROCENT') / 100);
                    $parent->network_cashback_bonus_count += $networkCashBackValue;
                    $parent->save();

                    Telegram::sendMessage([
                        'chat_id' => $parent->telegram_chat_id,
                        'parse_mode' => 'Markdown',
                        'text' => "Вам начислен сетевой CashBack $level_index уровня в размере $networkCashBackValue руб.",
                        'disable_notification' => 'false'
                    ]);
                }
            }
        }


    }
}
