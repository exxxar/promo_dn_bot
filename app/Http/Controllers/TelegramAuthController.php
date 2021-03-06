<?php

namespace App\Http\Controllers;

use App\Enums\AchievementTriggers;
use App\Events\AchievementEvent;
use App\User;
use Azate\LaravelTelegramLoginAuth\TelegramLoginAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramAuthController extends Controller
{
    /**
     * @var TelegramLoginAuth
     */
    protected $telegram;

    /**
     * AuthController constructor.
     *
     * @param TelegramLoginAuth $telegram
     */
    public function __construct(TelegramLoginAuth $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * Get user info and log in (hypothetically)
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function handleTelegramCallback()
    {

        if ($this->telegram->validate()) {

            $telegramUser = $this->telegram->user();
            $id = $telegramUser["id"];
            $username = $telegramUser["username"];
            $firstName = $telegramUser["first_name"];
            $lastName = $telegramUser["last_name"];

            $user = User::where("telegram_chat_id", $id)->first();

            Telegram::sendMessage([
                'chat_id' => $id,
                'parse_mode' => 'Markdown',
                'text' => "Спасибо что пользуетесь нашей системой!",
                'disable_notification' => 'true'
            ]);

            if (isset($user)) {
                if ($user->is_admin == 1) {
                    Auth::loginUsingId($user->id);
                    return redirect('/admin');
                }
            } else {
                $u = User::create([
                    'name' => $username ?? "$id",
                    'email' => "$id@t.me",
                    'password' => bcrypt($id),
                    'fio_from_telegram' => "$firstName $lastName",
                    'source' => "000",
                    'telegram_chat_id' => $id,
                    'referrals_count' => 0,
                    'referral_bonus_count' => 10,
                    'cashback_bonus_count' => 0,
                    'is_admin' => false,
                ]);

                event(new AchievementEvent(AchievementTriggers::MaxReferralBonusCount, 10, $user));


                if (!$u->onRefferal()) {
                    $skidobot = User::where("email", "skidobot@gmail.com")->first();
                    if ($skidobot) {
                        $skidobot->referrals_count += 1;
                        $skidobot->save();


                        $u->parent_id = $skidobot->id;
                        $u->save();
                    }

                }

                Auth::login($u);
            }
        }

        return redirect('/');

    }
}
