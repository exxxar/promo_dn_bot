<?php

namespace App\Http\Controllers;

use App\Conversations\FillInfoConversation;
use App\Conversations\LotteryConversation;
use App\Conversations\LotusProfileConversation;
use App\Conversations\PaymentConversation;
use App\Conversations\PromoConversation;
use App\Conversations\StartConversation;
use App\Conversations\StartDataConversation;
use App\Conversations\StopConversation;
use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;


class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        $botman = app('botman');

        $botman->listen();
    }

    public function testGetUpdates()
    {
        $activity = Telegram::getUpdates();
        dd($activity);
    }

    public function tinker()
    {
        return view('tinker');
    }

    public function lotusprofileConversation(BotMan $bot, $data)
    {
        $bot->startConversation(new LotusProfileConversation($bot, $data));
    }

    public function promoConversation(BotMan $bot, $data)
    {
        $bot->startConversation(new PromoConversation($bot, $data));
    }

    public function startConversation(BotMan $bot)
    {
        $bot->startConversation(new StartConversation($bot));
    }

    public function stopConversation(BotMan $bot)
    {
        $bot->startConversation(new StopConversation($bot));
    }

    public function startDataConversation(BotMan $bot, $data)
    {
        $bot->startConversation(new StartDataConversation($bot, $data));
    }

    public function fillInfoConversation($bot)
    {
        $bot->startConversation(new FillInfoConversation($bot));
    }

    public function paymentConversation(BotMan $bot, $request_id, $company_id)
    {
        $bot->startConversation(new PaymentConversation($bot, $request_id, $company_id));
    }

    public function lotteryConversation(BotMan $bot)
    {
        $bot->startConversation(new LotteryConversation($bot));
    }


}
