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
        if ($this->user->fio_from_request != "") {
            $this->askPhone1();
            return;
        }
        $question = Question::create('Как тебя зовут?')
            ->fallback('Спасибо что пообщался со мной:)!');

        $this->ask($question, function (Answer $answer) {
            $this->user->fio_from_request = $answer->getText();
            $this->user->save();

            $this->askPhone1();
        });
    }

    public function askPhone1()
    {
        if ($this->user->phone != null) {
            $this->askSex();
            return;
        }

        $question = Question::create('Скажие мне свой телефонный номер')
            ->fallback('Спасибо что пообщался со мной:)!');

        $this->ask($question, function (Answer $answer) {

            $vowels = array("(", ")", "-", " ");
            $tmp_phone = $answer->getText();

            Log::info($tmp_phone);

            $tmp_phone = str_replace($vowels, "", $tmp_phone);

            if (!strpos($tmp_phone, "+38"))
                $tmp_phone = "+38" . $tmp_phone;


            $pattern = "/^\+380\d{3}\d{2}\d{2}\d{2}$/";

            if (preg_match($pattern, $tmp_phone) == 0) {

                $this->bot->reply("Номер введен не верно...\n");
                $this->askPhone();
                return;
            } else {

                $tmp_user = User::where("phone", $tmp_phone)->first();

                if ($tmp_user == null) {

                    $this->user->phone = $tmp_phone;
                    $this->user->save();


                } else {
                    $this->bot->reply("Пользователь с таким номером уже и так наш друг:)\n");
                    $this->askPhone();
                    return;
                }

            }

            $this->askSex();
        });


    }

    public function askSex()
    {
        if ($this->user->sex != null) {
            $this->askBirthday();
            return;
        }

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


    }

    public function askBirthday()
    {
        if ($this->user->birthday != null) {
            $this->askCity();
            return;
        }

        $question = Question::create('Следующий вопрос - дата твоего рождения:')
            ->fallback('Спасибо что пообщался со мной:)!');

        $this->ask($question, function (Answer $answer) {
            $this->user->birthday = $answer->getText();
            $this->user->save();
            $this->askCity();

        });
    }

    public function askCity()
    {
        if ($this->user->address != null) {
            $this->saveData();
            return;
        }

        $question = Question::create('Из какого ты города?')
            ->fallback('Спасибо что пообщался со мной:)!');

        $this->ask($question, function (Answer $answer) {
            $this->user->address = $answer->getText();
            $this->user->save();

            $this->saveData();
        });

    }

    public function saveData()
    {
        $this->mainMenu("Отлично! Вы справились!");
    }


}
