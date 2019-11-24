<?php

namespace App\Conversations;

use App\Promotion;
use App\UserHasPromo;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class PromoConversation extends Conversation
{
    protected $data;
    protected $bot;

    public function __construct($bot, $data)
    {
        $this->bot = $bot;
        $this->data = $data;
    }

    public function run()
    {
        $telegramUser = $this->bot->getUser();
        $id = $telegramUser->getId();

        $this->user = \App\User::where("telegram_chat_id", $id)
            ->first();

        $this->askForStartPromo();

    }

    public function askForStartPromo()
    {
        $question = Question::create('Хочешь поучаствовать в акции?')
            ->fallback('Ничего страшного, в следующий раз получится!')
            ->addButtons([
                Button::create('Да, хочу')->value('yes'),
                Button::create('нет, в другой раз')->value('no'),
            ]);

        $this->ask($question, function (Answer $answer) {
            // Detect if button was clicked:
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue(); // will be either 'yes' or 'no'
                $selectedText = $answer->getText(); // will be either 'Of course' or 'Hell no!'

                if ($selectedValue == "yes") {
                    $this->askFirstname();
                }

                if ($selectedValue == "no") {
                    $this->say("Хорошего дня!");
                }
            }
        });
    }

    public function askFirstname()
    {
        if ($this->user->fio_from_request == "") {
            $question = Question::create('Как тебя зовут?')
                ->fallback('Спасибо что пообщался со мной:)!');

            $this->ask($question, function (Answer $answer) {
                $this->user->fio_from_request = $answer->getText();
                $this->user->save();

                $this->say('Отлично, приятно познакомится ' . $this->username);
                $this->askPhone();
            });
        } else
            $this->askPhone();
    }

    public function askPhone()
    {
        if ($this->user->phone == "") {
            $question = Question::create('Скажие мне свой телефонный номер')
                ->fallback('Спасибо что пообщался со мной:)!');

            $this->ask($question, function (Answer $answer) {
                $this->user->phone = $answer->getText();
                $this->user->save();

                $this->askSex();

            });
        } else
            $this->askSex();

    }

    public function askSex()
    {
        if ($this->user->sex == null) {
            $question = Question::create('А какого ты пола?')
                ->fallback('Спасибо что пообщался со мной:)!')
                ->addButtons([
                    Button::create("\xF0\x9F\x91\xA6Мальчик")->value('man'),
                    Button::create("\xF0\x9F\x91\xA7Девочка")->value('woman'),
                ]);

            $this->ask($question, function (Answer $answer) {
                // Detect if button was clicked:
                if ($answer->isInteractiveMessageReply()) {

                    $this->user->sex = $answer->getValue() == "man" ? 0 : 1;
                    $this->user->save();

                    $this->askCity();

                }
            });
        } else

            $this->askCity();


    }

    public function askCity()
    {
        if ($this->user->address == '') {
            $question = Question::create('Из какого ты города?')
                ->fallback('Спасибо что пообщался со мной:)!');

            $this->ask($question, function (Answer $answer) {
                $this->user->address = $answer->getText();
                $this->user->save();

                $this->askAge();
            });
        } else
            $this->askAge();
    }

    public function askAge()
    {
        if ($this->user->age == null) {
            $question = Question::create('Последний вопрос - сколько тебе лет?')
                ->fallback('Спасибо что пообщался со мной:)!');

            $this->ask($question, function (Answer $answer) {
                $this->user->age = $answer->getText();
                $this->user->save();

                $this->saveData();
            });
        } else
            $this->saveData();
    }

    public function saveData()
    {

        $promo = Promotion::find(intval($this->data));

        if ($promo->current_activation_count < $promo->activation_count) {

            if ($promo->immediately_activate == 1) {
                $this->user->referral_bonus_count += $promo->refferal_bonus;
                $this->bot->reply($promo->activation_text);
            }
        }


        $this->user->save();


        if ($promo->immediately_activate == 0) {
            $this->bot->reply("Спасибо! Получите свои бонусы у нашего сотрудника по этому QR-коду.");

            $tmp_id = $this->user->telegram_chat_id;
            while (strlen($tmp_id) < 10)
                $tmp_id = "0" . $tmp_id;

            $tmp_promo_id = $this->data;
            while (strlen($tmp_promo_id) < 10)
                $tmp_promo_id = "0" . $tmp_promo_id;

            $code = base64_encode("003" . $tmp_id . $tmp_promo_id);
            $tmp_img = substr($code, 0, strlen($code) - 2);


            $this->bot->reply(env("APP_URL") . "/image/" . $tmp_img,
                ["parse_mode" => "Markdown"]);
        }

    }
}
