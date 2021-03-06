<?php

namespace App\Conversations;

use App\Classes\BaseBot;
use App\Classes\SkidkiBotMenu;
use App\User;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Support\Facades\Log;

class FillInfoConversation extends Conversation
{
    use BaseBot;


    public function __construct($bot)
    {
        $this->setBot($bot);
    }

    public function run()
    {
        try {
            $this->conversationMenu(__('messages.menu_title_2'));
            $this->askFirstname();
        } catch (\Exception $e) {
            Log::error(get_class($this));
            $this->mainMenu(__('messages.menu_title_1'));
        }
    }


    public function askFirstname()
    {
        if (strlen(trim($this->getUser()->fio_from_request)) > 0) {
            $this->askPhone();
            return;
        }
        $question = Question::create(__("messages.ask_name"))
            ->fallback(__("messages.ask_fallback"));

        $this->ask($question, function (Answer $answer) {
            $user = $this->getUser();
            $user->fio_from_request = $answer->getText() ?? '';
            $user->save();
            $this->askPhone();
        });
    }

    public function askPhone()
    {
        if (!is_null($this->getUser()->phone) && strlen(trim($this->getUser()->phone)) > 0) {
            $this->askSex();
            return;
        }

        $question = Question::create(__("messages.ask_phone"))
            ->fallback(__("messages.ask_fallback"));

        $this->ask($question, function (Answer $answer) {

            $vowels = array("(", ")", "-", " ");
            $tmp_phone = str_replace($vowels, "", $answer->getText());
            $tmp_phone = strpos($tmp_phone, "+38") === false ?
                "+38" . $tmp_phone :
                $tmp_phone;

            $pattern = "/^\+380\d{3}\d{2}\d{2}\d{2}$/";

            if (strlen($tmp_phone) > 13) {
                $this->reply(__("messages.ask_phone_error_1"));
                $this->askPhone();
                return;
            }

            if (preg_match($pattern, $tmp_phone) == 0) {

                $this->reply(__("messages.ask_phone_error_1"));
                $this->askPhone();
                return;
            } else {

                $tmp_user = User::where("phone", $tmp_phone)->first();

                if (is_null($tmp_user)) {
                    $user = $this->getUser();
                    $user->phone = $tmp_phone;
                    $user->save();
                } else {
                    $this->reply(__("messages.ask_phone_error_2"));
                    $this->askPhone();
                    return;
                }
            }
            $this->askSex();
        });


    }

    public function askSex()
    {
        if (!is_null($this->getUser()->sex)) {
            $this->askBirthday();
            return;
        }

        $question = Question::create(__('messages.ask_sex'))
            ->fallback(__('messages.ask_fallback'))
            ->addButtons([
                Button::create(__("messages.ask_sex_btn_1"))->value('man'),
                Button::create(__("messages.ask_sex_btn_2"))->value('woman'),
            ]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $user = $this->getUser();
                $user->sex = ($answer->getValue() == "man" ? 0 : 1) ?? 0;
                $user->save();
                $this->askBirthday();
            }
        });


    }

    public function askBirthday()
    {
        if (!is_null($this->getUser()->birthday) && strlen(trim($this->getUser()->birthday)) > 0) {
            $this->askCity();
            return;
        }

        $question = Question::create(__("messages.ask_birthday"))
            ->fallback(__("messages.ask_fallback"));

        $this->ask($question, function (Answer $answer) {
            $user = $this->getUser();
            $user->birthday = $answer->getText() ?? '';
            $user->save();
            $this->askCity();
        });
    }

    public function askCity()
    {
        if (!is_null($this->getUser()->address) && strlen(trim($this->getUser()->address)) > 0) {
            $this->saveData();
            return;
        }

        $question = Question::create(__("messages.ask_city"))
            ->fallback(__("messages.ask_fallback"));

        $this->ask($question, function (Answer $answer) {
            $user = $this->getUser();
            $user->address = $answer->getText() ?? '';
            $user->save();
            $this->saveData();
        });

    }


    public function saveData()
    {
        $this->mainMenu(__("messages.menu_title_4"));
    }


}
