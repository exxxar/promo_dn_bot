<?php


namespace App\Classes;


use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;

class TestApiBot
{
    use ApiBot;

    protected $business_school_main_keyboard = [
        [
            "Про Школу бизнеса", "Мероприятия"
        ],
        [
            "Боты для Вашего бизнеса", "Маркетинг 2.0"
        ],
        [
            "Бизнес и личностный рост", "Личностный рост 4+1"
        ],
        [
            "Обо мне", "Rest Service"
        ]

    ];

    public function __construct($botName, $chatId)
    {
        $this
            ->setBot($botName)
            ->setChatId($chatId);
    }

    public function menu(){
       // $this->sendMessage("Главное меню");
        $this->sendMenu("Главное меню системы",$this->business_school_main_keyboard);
    }

    public function menu2($arg1,$arg2,$arg3){
        $this->sendMessage("data $arg2 $arg3");
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

    public function mainMenu()
    {
        $this->sendMenu("*Главное меню*", $this->business_school_main_keyboard);
    }
}
