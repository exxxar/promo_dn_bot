<?php

namespace App\Listeners;

use App\CashBackInfo;
use App\Company;
use App\Enums\AchievementTriggers;
use App\Events\AchievementEvent;
use App\Events\ActivateUserEvent;
use App\Events\AddCashBackEvent;
use App\Events\NetworkCashBackEvent;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Telegram\Bot\Laravel\Facades\Telegram;

class CashBackHandler
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
    public function handle(AddCashBackEvent $event)
    {
        $user = User::find($event->user_id);
        $company = Company::find($event->company_id);
        $cashBack = round(intval($event->money_in_check) * $company->cashback / 100);
        $companyName = $company->title ?? "Неизвестная компания";

        if (env("INDIVIDUAL_CASHBACK_MODE") == false) {
            $user->cashback_bonus_count += $cashBack;
            $user->save();
        } else
            if (env("INDIVIDUAL_CASHBACK_MODE") == true) {
                $cbi = CashBackInfo::where("company_id", $event->company_id)
                    ->where("user_id", $event->user_id)
                    ->first();

                if (is_null($cbi)) {
                    CashBackInfo::create([
                        'user_id' => $event->user_id,
                        'company_id' => $event->company_id,
                        'value' => $cashBack
                    ]);
                } else {
                    $cbi->value += $cashBack;
                    $cbi->save();
                }
            }

        event(new ActivateUserEvent($user));
        event(new NetworkCashBackEvent($event->user_id, $cashBack));
        event(new AchievementEvent(
                AchievementTriggers::MaxCashBackCount,
                $cashBack,
                $user
            )
        );

        Telegram::sendMessage([
            'chat_id' => $user->telegram_chat_id,
            'parse_mode' => 'Markdown',
            'text' => "Сумма в чеке *$event->money_in_check* руб.\nВам начислен *CashBack* в размере *$cashBack* руб от компании *$companyName*",
            'disable_notification' => 'false'
        ]);
    }
}
