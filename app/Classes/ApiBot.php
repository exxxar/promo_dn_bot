<?php


namespace App\Classes;


use App\BotHub;
use App\Enums\AchievementTriggers;
use App\Events\AchievementEvent;
use App\User;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\TelegramClient;

trait ApiBot
{

    protected $bot;

    protected $telegram_user;

    protected $bot_params;

    protected $message_id;

    protected $keyboard_fallback = [
        [
            "Попробовать снова"
        ],
    ];

    public function setBot($botName)
    {
        $this->bot_params = BotHub::where("bot_url", $botName)
            ->first();

        if (is_null($this->bot_params))
            return;

        try {
            $this->bot = new Api(config("app.debug") ? $this->bot_params->token_dev : $this->bot_params->token_prod, true);
        } catch (TelegramSDKException $e) {
            Log::error($e->getMessage() . " " . $e->getLine());
        }

        return $this;
    }

    public function getChatId()
    {
        return $this->telegram_user->id;
    }

    public function reply($message)
    {
        $this->sendMessage($message);
    }

    public function setLastMessageId($message_id = null){
        $this->message_id = $message_id;
        return $this;
    }
    public function setTelegramUser($telegram_user)
    {
        $this->telegram_user = json_decode($telegram_user);

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

   /* public function createNewUser()
    {

        $id = $this->bot->getUser()->getId() ?? null;
        $username = $this->bot->getUser()->getUsername();
        $lastName = $this->bot->getUser()->getLastName();
        $firstName = $this->bot->getUser()->getFirstName();

        if ($id == null)
            return false;

        if ($this->getUser() == null) {
            $user = User::create([
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
            return true;


        }

        if (!$this->getUser()->onRefferal()) {
            $skidobot = User::where("email", "skidobot@gmail.com")->first();
            if ($skidobot) {
                $skidobot->referrals_count += 1;
                $skidobot->save();

                $user = $this->getUser();
                $user->parent_id = $skidobot->id;
                $user->save();
            }

        }
        return false;
    }*/

    public function sendMessage($message, $keyboard = [], $parseMode = 'Markdown')
    {

        if (is_null($this->bot))
            return;

        $this->bot->sendMessage([
            "chat_id" => $this->telegram_user->id,
            "text" => $message,
            'parse_mode' => $parseMode,
            'reply_markup' => json_encode([
                'inline_keyboard' => $keyboard
            ])
        ]);

    }

    public function editMessageText($text = "empty")
    {
        if (is_null($this->bot))
            return;

        $this->bot->editMessageText([
            'text' => $text,
            'chat_id' => $this->getChatId(),
            "message_id" => $this->message_id
        ]);
    }

    public function editReplyKeyboard($keyboard=[]){

        if (is_null($this->bot))
            return;

        $this->bot->editMessageReplyMarkup([
            'chat_id' => $this->getChatId(),
            "message_id" => $this->message_id,
            'reply_markup' => json_encode([
                'inline_keyboard' => $keyboard,
            ])
        ]);
    }

    public function sendPhoto($message, $photoUrl, $keyboard = [], $parseMode = 'Markdown')
    {
        if (is_null($this->bot))
            return;

        $this->bot->sendPhoto([
            'chat_id' => $this->telegram_user->id,
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
            "chat_id" =>$this->telegram_user->id,
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
