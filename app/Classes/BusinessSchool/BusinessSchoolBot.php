<?php


namespace App\Classes\BusinessSchool;


use App\Classes\ApiBot;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;

class BusinessSchoolBot implements iBusinessSchoolBot
{
    use ApiBot, tBusinessSchoolMenu;

    public function __construct($botName, $telegram_user, $message_id = null)
    {
        $this
            ->setBot($botName)
            ->setTelegramUser($telegram_user)
            ->setLastMessageId($message_id);
    }

    public function start()
    {
        $this->mainMenu("Главное меню");
    }

    public function getAboutBusinessSchoolPage()
    {
        $this->sendMessage("getAboutBusinessSchoolPage");
        $this->reply("message id" . ($this->message_id ?? 'empty'));

    }

    public function getEventsPage()
    {
        $this->sendMessage("getEventsPage");
    }

    public function getBotForBusinessPage()
    {
        $this->sendMessage("getBotForBusinessPage");
    }

    public function aboutBusinessSchool()
    {
        $this->sendMessage("aboutBusinessSchool");
    }

    public function getBusinessPersonalGrowthPage()
    {
        $this->sendMessage("getBusinessPersonalGrowthPage");
    }

    public function getPersonalGrowthPage()
    {
        $this->sendMessage("getPersonalGrowthPage");
    }

    public function getAboutMePage()
    {
        $this->sendMessage("getAboutMePage");
    }

    public function getRestServicePage()
    {
        $this->sendMessage("getRestServicePage");
    }

}
