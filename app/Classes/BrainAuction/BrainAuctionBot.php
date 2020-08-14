<?php


namespace App\Classes\BusinessSchool;


use App\Classes\ApiBot;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;

class BrainAuctionBot implements iBrainAuctionBot
{
    use ApiBot, tBrainAuctionMenu;

    public function __construct($botName, $chatId)
    {
        $this
            ->setBot($botName)
            ->setChatId($chatId);
    }

    public function start(){
        $this->mainMenu("Главное меню");
    }

    public function getAboutBusinessSchoolPage(){
        $this->sendMessage("getAboutBusinessSchoolPage");
    }

    public function getEventsPage(){
        $this->sendMessage("getEventsPage");
    }

    public function getBotForBusinessPage(){
        $this->sendMessage("getBotForBusinessPage");
    }

    public function aboutBusinessSchool(){
        $this->sendMessage("aboutBusinessSchool");
    }

    public function getBusinessPersonalGrowthPage(){
        $this->sendMessage("getBusinessPersonalGrowthPage");
    }

    public function getPersonalGrowthPage(){
        $this->sendMessage("getPersonalGrowthPage");
    }

    public function getAboutMePage(){
        $this->sendMessage("getAboutMePage");
    }

    public function getRestServicePage(){
        $this->sendMessage("getRestServicePage");
    }

}
