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

    protected $keyboard_fallback = [
        [
            "Попробовать снова"
        ],
    ];

    public function setBot($botName)
    {
        $bot = BotHub::where("bot_url", $botName)
            ->first();
        try {
            $this->bot = new Api(config("app.debug") ? $bot->token_dev : $bot->token_prod,true);

            if ($bot->is_active==0){
                $this->sendMenu("Бот в данный момент недоступен!",$this->keyboard_fallback);
                $this->bot = null;
            }
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

        if (is_null($this->bot))
            return;

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
        if (is_null($this->bot))
            return;

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
        if (is_null($this->bot))
            return;

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
