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

    protected $bot;

    public function setBot($bot)
    {
        $this->bot = $bot;
        $this->createNewUser();
        return $this;
    }

    protected function createNewUser()
    {

        $id = $this->bot->getUser()->getId();
        $username = $this->bot->getUser()->getUsername();
        $lastName = $this->bot->getUser()->getLastName();
        $firstName = $this->bot->getUser()->getFirstName();

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

            $this->
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
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xA9Далее", 'callback_data' => "$command " . ($page + 1)]);
        if ($page > 0) {
            if (count($model) == 0) {
                array_push($inline_keyboard, ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => "$command " . ($page - 1)]);
            }
            if (count($model) == config("bot.results_per_page")) {
                array_push($inline_keyboard, ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => "$command " . ($page - 1)]);
                array_push($inline_keyboard, ['text' => "\xE2\x8F\xA9Далее", 'callback_data' => "$command " . ($page + 1)]);
            }
            if (count($model) > 0 && count($model) < config("bot.results_per_page")) {
                array_push($inline_keyboard, ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => "$command " . ($page - 1)]);
            }
        }

        if (count($inline_keyboard) > 0)
            $this->sendMessage($resultMessage, $inline_keyboard);

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

    protected function sendPhoto($message, $photoUrl, array $keyboard = [], $parseMode = 'Markdown')
    {
        $this->bot->sendRequest("sendPhoto",
            [
                "chat_id" => $this->getChatId(),
                "photo" => $photoUrl,
                "caption" => $message,
                'parse_mode' => $parseMode,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $keyboard
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
        $this->sendMenu("Добрый день!Приветствуем вас в нашем акционном боте! Мы рады, что Вы присоединились к нам. Все акции будут активны с 17 января.",
            $this->keyboard);

        $categories = Category::orderBy('id', 'DESC')
            ->orderBy('position', 'DESC')
            ->get();

        if (count($categories) == 0) {
            $this->reply("К сожалению, сейчас акций нет, но они появятся в ближайшее время!");
            return;
        }
        $keyboard = [];

        foreach ($categories as $cat)
            array_push($keyboard, [
                ["text" => $cat->title, "callback_data" => "/category " . $cat->id]
            ]);

        $this->sendMessage("Категории акций:", $keyboard);
    }

}
