<?php
/**
 * Created by PhpStorm.
 * User: exxxa
 * Date: 24.11.2019
 * Time: 20:57
 */

namespace App\Conversations;


use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;


trait CustomConversation
{
    protected $keyboard = [
        ["\xE2\x9B\x84Новый год"],
        ["\xF0\x9F\x93\xB2QR", "\xF0\x9F\x92\xB3Баллы", "\xF0\x9F\x92\xB0Кэшбэк"],
        ["\xF0\x9F\x94\xA5Все категории", "\xF0\x9F\x94\xA5Все компании"],
        ["\xE2\x9A\xA1Все акции"]
    ];

}