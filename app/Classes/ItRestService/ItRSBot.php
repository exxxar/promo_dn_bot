<?php


namespace App\Classes\BusinessSchool;


use App\Classes\ApiBot;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;

class ItRSBot implements iItRSBot
{
    use ApiBot, tItRSMenu;

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

        if (!$this->hasInStorage("conversation"))
            $this->addToStorage('conversation','true');

    }

    public function getEventsPage()
    {
        $this->sendMessage("getEventsPage",[
            [
                ["text"=>"EDIT ME","callback_data"=>"/edit_events_page 1"]
            ]
        ]);
    }

    public function editEventsPage($arg0,$page){
        $this->editReplyKeyboard([
            [
                ["text"=>"EDITED! $page","callback_data"=>"/edit_events_page ".($page+1)]
            ]
        ]);
    }

    public function getBotForBusinessPage()
    {
        $this->sendMessage("getBotForBusinessPage");

       /* $this->startConversation([

        ]);*/
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
        $this->clearStorage();
    }

    public function getRestServicePage()
    {
        $this->sendMessage("getRestServicePage");

       // if (Session::has('conversation'))
            $this->sendMessage("c:".$this->getFromStorage('conversation','false'));

    }

}
