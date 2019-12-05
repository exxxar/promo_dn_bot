<?php

namespace App\Http\Controllers;

use App\User;
use Azate\LaravelTelegramLoginAuth\TelegramLoginAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            $user = User::where("telegram_chat_id",$this->telegram->user()->id)->first();
            Auth::login($user, true);
        }

        return redirect('/');
    }
}
