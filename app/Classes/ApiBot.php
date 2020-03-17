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

    protected $bot_params;

    protected $keyboard_fallback = [
        [
            "Попробовать снова"
        ],
    ];

    public function setBot($botName)
    {
        $this->bot_params = BotHub::where("bot_url", $botName)
            ->first();

        try {
            $this->bot = new Api(config("app.debug") ? $this->bot_params->token_dev : $this->bot_params->token_prod, true);
        } catch (TelegramSDKException $e) {
            Log::error($e->getMessage() . " " . $e->getLine());
        }

        return $this;
    }

    public function setChatId($chatId)
    {
        $this->chatId = $chatId;

        if ($this->bot_params->is_active == 0) {

            $keyboard = [
                [
                    ["text"=>"\xF0\x9F\x92\xB3Оплатить услуги сервиса","url"=>"https://www.free-kassa.ru/merchant/cash.php?m=169322&oa=200&s=c6fbd62b3825c451c1d6eabb22b34a5d&o=2005849"]
                ]
            ];


            $this->sendPhoto('', 'https://sun9-29.userapi.com/c858232/v858232349/173635/lTlP7wMcZEA.jpg',$keyboard);
            $this->sendMenu("Бот в данный момент недоступен!", $this->keyboard_fallback);
            $this->bot = null;
        }

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

    public function sendPhoto($message, $photoUrl, $keyboard = [], $parseMode = 'Markdown')
    {
        if (is_null($this->bot))
            return;

        $this->bot->sendPhoto([
            'chat_id' => $this->chatId,
            'parse_mode' => $parseMode,
            'caption' => $message,
            'photo' => InputFile::create($photoUrl),
            'disable_notification' => 'true',
            'reply_markup' => json_encode([
                'inline_keyboard' => $keyboard
            ])
        ]);
    }

    public function sendMenu($message, $keyboard)
    {
        if (is_null($this->bot))
            return;

        $this->bot->sendMessage([
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
