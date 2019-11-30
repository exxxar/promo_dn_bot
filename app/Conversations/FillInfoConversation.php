<?php

namespace App\Conversations;

use App\User;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class FillInfoConversation extends Conversation
{
    use CustomConversation;

    protected $bot;

    public function __construct($bot)
    {
        $this->bot = $bot;
    }

    public function run()
    {
        $telegramUser = $this->bot->getUser();
        $id = $telegramUser->getId();

        $this->user = \App\User::where("telegram_chat_id", $id)
            ->first();


        $this->conversationMenu("Начнем-с...");
        $this->askFirstname();

    }


    public function askFirstname()
    {
        if ($this->user->fio_from_request == "") {
            $question = Question::create('Как тебя зовут?')
                ->fallback('Спасибо что пообщался со мной:)!');

            $this->ask($question, function (Answer $answer) {
                $this->user->fio_from_request = $answer->getText();
                $this->user->save();

                $this->askPhone1();
            });
        } else
            $this->askPhone1();
    }

    public function askPhone1()
    {
        if ($this->user->phone == null) {
            $question = Question::create('Скажие мне свой телефонный номер')
                ->fallback('Спасибо что пообщался со мной:)!');

            $this->ask($question, function (Answer $answer) {

                $vowels = array("(", ")", "-", " ");
                $tmp_phone = $answer->getText();


                $tmp_phone = str_replace($vowels, "", $tmp_phone);


                if (!strpos($tmp_phone, "+38"))
                    $tmp_phone = "+38" . $tmp_phone;

                $tmp_user = User::where("phone", $tmp_phone)->first();

                if ($tmp_user == null) {

                    $this->user->phone = $tmp_phone;
                    $this->user->save();


                } else
                    $this->mainMenu("Пользователь с таким номером уже и так наш друг:)\n");

                $this->askSex();
            });
        }
        else
            $this->askSex();

    }

    public function askSex()
    {
        if (!is_int($this->user->sex)) {
            $question = Question::create('А какого ты пола?')
                ->fallback('Спасибо что пообщался со мной:)!')
                ->addButtons([
                    Button::create("\xF0\x9F\x91\xA6Парень")->value('man'),
                    Button::create("\xF0\x9F\x91\xA7Девушка")->value('woman'),
                ]);

            $this->ask($question, function (Answer $answer) {
                // Detect if button was clicked:
                if ($answer->isInteractiveMessageReply()) {

                    $this->user->sex = $answer->getValue() == "man" ? 0 : 1;
                    $this->user->save();

                    $this->askBirthday();
                }
            });
        } else
            $this->askBirthday();


    }

    public function askBirthday()
    {
        if ($this->user->birthday == null) {
            $question = Question::create('Следующий вопрос - дата твоего рождения:')
                ->fallback('Спасибо что пообщался со мной:)!');

            $this->ask($question, function (Answer $answer) {
                $this->user->birthday = $answer->getText();
                $this->user->save();
                $this->askCity();

            });
        } else
            $this->askCity();
    }

    public function askCity()
    {
        if ($this->user->address == null) {
            $question = Question::create('Из какого ты города?')
                ->fallback('Спасибо что пообщался со мной:)!');

            $this->ask($question, function (Answer $answer) {
                $this->user->address = $answer->getText();
                $this->user->save();

                $this->saveData();
            });
        } else
            $this->saveData();
    }

    public function saveData()
    {
        $this->mainMenu("Отлично! Вы справились!");
    }


}
