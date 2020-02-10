<?php


namespace App\Classes;


use App\Category;
use App\Enums\AchievementTriggers;
use App\Events\AchievementEvent;
use App\RefferalsHistory;
use App\User;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;

abstract class Bot
{
    use CustomBotMenu;
}
