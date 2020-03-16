<?php


namespace App\Classes;


use App\BotHub;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\TelegramClient;

trait ApiBot
{

    protected $bot;

    protected $chatId;

    public function setBot($botName)
    {
        $bot = BotHub::where("bot_url", $botName)
            ->first();

        try {
            $this->bot = new Api(config("app.debug") ? $bot->token_dev : $bot->token_prod,true);
           //$this->bot = new Api(env("TELEGRAM_LOGGER_BOT_TOKEN"),true);
        } catch (TelegramSDKException $e) {
            Log::error($e->getMessage() . " " . $e->getLine());
        }

        return $this;
    }

    public function setChatId($chatId)
    {
        $this->chatId = $chatId;
        return $this;
    }

    public function sendMessage($message, $keyboard = [], $parseMode = 'Markdown')
    {

        $this->bot->sendMessage([
            "chat_id" => $this->chatId,
            "text" => $message,
            'parse_mode' => $parseMode,
            'reply_markup' => json_encode([
                'inline_keyboard' => $keyboard
            ])
        ]);

    }

    public function sendPhoto($message, $photoUrl, $parseMode = 'Markdown')
    {
        $this->bot->sendPhoto([
            'chat_id' =>$this->chatId,
            'parse_mode' => $parseMode,
            'caption' => $message,
            'photo' => InputFile::create($photoUrl),
            'disable_notification' => 'true'
        ]);
    }

    public function sendMenu($message, $keyboard)
    {
        $this->bot->sendMessage( [
            "chat_id" => $this->chatId,
            "text" => $message,
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'keyboard' => $keyboard,
                'one_time_keyboard' => false,
                'resize_keyboard' => true
            ])
        ]);
    }
}
