<?php

namespace App\Listeners;

use App\Events\NetworkCashBackEvent;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Telegram\Bot\Laravel\Facades\Telegram;

class NetworkCashBackProcessor
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(NetworkCashBackEvent $event)
    {
        $user = User::with(["parent"])
            ->where('id', $event->userId)
            ->first();

        $user->network_cashback_bonus_count += ($event->cashBackValue * env('CAHSBAK_NETWORK_PROCENT') / 100);
        $user->save();

        for ($level_index = 1; $level_index <= 3; $level_index++) {

            switch ($level_index) {
                default:
                case 1:
                    $parent = $user->parent;
                    break;
                case 2:
                    $parent = $user->parent->parent;
                    break;
                case 3:
                    $parent = $user->parent->parent->parent;
                    break;
            }
            if ($parent != null)
                if ($parent->current_network_level >= $level_index &&
                    $parent->current_network_level > 0 &&
                    $parent->activated == 1) {
                    $networkCashBackValue = ($event->cashBackValue * env('CAHSBAK_NETWORK_PROCENT') / 100);
                    $parent->network_cashback_bonus_count +=$networkCashBackValue ;
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

    /**
     * Handle the event.
     *
     * @param  object $event
     * @return void
     */
    public function handle($event)
    {
        //
    }
}
