<?php


namespace App\Classes;


use App\Category;
use App\Enums\AchievementTriggers;
use App\Events\AchievementEvent;
use App\User;
use Carbon\Carbon;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;

trait CustomBotMenu
{
    protected $bot;

    protected $keyboard;

    protected $keyboard_fallback = [
        ["Попробовать снова"],
    ];

    protected $keyboard_conversation = [
        ["Продолжить позже"],
    ];

    public function initKeyboards()
    {
        $this->keyboard = [
            [__("messages.global_menu_3"), __("messages.global_menu_5")],
            [__("messages.global_menu_1"), __("messages.global_menu_2")],
            [__("messages.global_menu_4")]
        ];
    }


    public function setBot($bot)
    {
        $this->bot = $bot;
        $this->createNewUser();
        return $this;
    }

    protected function createNewUser()
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
    }

    public function getUser(array $params = [])
    {
        return (count($params) == 0 ?
                User::where("telegram_chat_id", $this->getChatId())->first() :
                User::with($params)->where("telegram_chat_id", $this->getChatId())->first()) ?? null;


    }

    public function reply($message)
    {
        //$this->bot->reply($message);
        $this->sendMessage($message);
    }

    protected function getChatId()
    {
        return ($this->bot->getUser())->getId();
    }

    public function pagination($command, $model, $page, $resultMessage)
    {
        $inline_keyboard = [];

        if ($page == 0 && count($model) == config("bot.results_per_page"))
            array_push($inline_keyboard, ['text' => __("messages.action_next_btn"), 'callback_data' => "$command " . ($page + 1)]);
        if ($page > 0) {
            if (count($model) == 0) {
                array_push($inline_keyboard, ['text' => __("messages.action_prew_btn"), 'callback_data' => "$command " . ($page - 1)]);
            }
            if (count($model) == config("bot.results_per_page")) {
                array_push($inline_keyboard, ['text' => __("messages.action_prew_btn"), 'callback_data' => "$command " . ($page - 1)]);
                array_push($inline_keyboard, ['text' => __("messages.action_next_btn"), 'callback_data' => "$command " . ($page + 1)]);
            }
            if (count($model) > 0 && count($model) < config("bot.results_per_page")) {
                array_push($inline_keyboard, ['text' => __("messages.action_prew_btn"), 'callback_data' => "$command " . ($page - 1)]);
            }
        }

        if (count($inline_keyboard) > 0)
            $this->sendMessage($resultMessage, [$inline_keyboard]);

    }

    protected function sendMessage($message, array $keyboard = [], $parseMode = 'Markdown')
    {
        $this->bot->sendRequest("sendMessage",
            [
                "chat_id" => $this->getChatId(),
                "text" => $message,
                'parse_mode' => $parseMode,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $keyboard
                ])
            ]);
    }

    protected function sendMessageToChat($chatId, $message, array $keyboard = [], $parseMode = 'Markdown')
    {
        $this->bot->sendRequest("sendMessage",
            [
                "chat_id" => $chatId,
                "text" => $message,
                'parse_mode' => $parseMode,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $keyboard
                ])
            ]);
    }

    protected function sendPhoto($message, $photoUrl, array $keyboard = [], $parseMode = 'Markdown')
    {
        $this->bot->sendRequest("sendPhoto",
            [
                "chat_id" => $this->getChatId(),
                "photo" => $photoUrl,
                "caption" => $message,
                'parse_mode' => $parseMode,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $keyboard,
                ])
            ]);
    }

    protected function sendLocation($latitude, $longitude, array $keyboard = [])
    {
        $this->bot->sendRequest("sendLocation",
            [
                "chat_id" => $this->getChatId(),
                "latitude" => $latitude,
                "longitude" => $longitude,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $keyboard,
                ])
            ]);
    }

    protected function sendMenu($message, $keyboard)
    {

        $this->bot->sendRequest("sendMessage", [
            "text" => $message,
            'reply_markup' => json_encode([
                'keyboard' => $keyboard,
                'one_time_keyboard' => false,
                'resize_keyboard' => true
            ])
        ]);
    }

    public function mainMenu($message)
    {
        $this->initKeyboards();
        $this->sendMenu($message, $this->keyboard);
    }

    public function conversationMenu($message)
    {
        $this->sendMenu($message, $this->keyboard_conversation);
    }

    public function fallbackMenu($message)
    {
        $this->sendMenu($message, $this->keyboard_fallback);
    }

    protected function sendPhotoToChanel($message, $photoUrl, $parseMode = 'Markdown')
    {
        Telegram::sendPhoto([
            'chat_id' => env("CHANNEL_ID"),
            'parse_mode' => $parseMode,
            'caption' => $message,
            'photo' => InputFile::create($photoUrl),
            'disable_notification' => 'true'
        ]);
    }

    public function startMenuWithCategories()
    {
        $this->sendMenu( __("messages.menu_title_8"),
            $this->keyboard);

        $categories = Category::orderBy('id', 'DESC')
            ->orderBy('position', 'DESC')
            ->get();

        if (count($categories) == 0) {
            $this->reply(__("messages.promo_message_1"));
            return;
        }
        $keyboard = [];

        foreach ($categories as $cat)
            array_push($keyboard, [
                ["text" => $cat->title, "callback_data" => "/category " . $cat->id]
            ]);

        $this->sendMessage(__("messages.promo_message_2"), $keyboard);
    }

    public function startConversation($conversaton)
    {
        $this->bot->startConversation($conversaton);
    }
}
