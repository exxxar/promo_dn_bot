<?php
/**
 * Created by PhpStorm.
 * User: exxxa
 * Date: 03.12.2019
 * Time: 21:06
 */

namespace App\Drivers;

use BotMan\BotMan\Drivers\HttpDriver;
use BotMan\BotMan\Interfaces\DriverInterface;
use BotMan\BotMan\Interfaces\UserInterface;
use BotMan\BotMan\Interfaces\WebAccess;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Users\User;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


use Illuminate\Support\Collection;
use Telegram\Bot\Laravel\Facades\Telegram;

class CustomeTelegramDriver extends TelegramDriver
{


    public function messagesHandled()
    {
        $callback = $this->payload->get('callback_query');
    }

}
