<?php

namespace App\Conversations;

use App\Classes\CustomBotMenu;
use App\User;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Support\Facades\Log;

class FillInstagramConversation extends Conversation
{
    use CustomBotMenu;


    public function __construct($bot)
    {
        $this->setBot($bot);
    }

    public function run()
    {
        try {
            $this->conversationMenu("Введите на ваш профиль");
            $this->askProfileName();
        } catch (\Exception $e) {
            Log::error(get_class($this));
            $this->mainMenu(__('messages.menu_title_1'));
        }
    }


    public function askProfileName()
    {
        $question = Question::create(__("messages.ask_instagram"))
            ->fallback(__("messages.ask_fallback"));

        $this->ask($question, function (Answer $answer) {
            $user = $this->getUser();
            $instagram = $answer->getText();
            if (is_null($instagram))
            {
                $this->askProfileName();
                return;
            }
            $exist = User::where("instagram",$instagram)->first();
            if (!is_null($exist))
            {
                $this->reply("Пользователь с таким аккаунтом уже есть!");
                $this->askProfileName();
                return;
            }

            $user->instagram = $instagram ?? null;
            $user->save();

            $this->mainMenu(__("messages.menu_title_4"));
        });
    }
}
