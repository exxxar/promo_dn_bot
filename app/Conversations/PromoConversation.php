<?php

namespace App\Conversations;

use App\Promotion;
use App\UserHasPromo;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
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
                Button::create('Детали акции')->value('promo_info'),
                Button::create('Да, хочу')->value('yes'),
                Button::create('Нет, в другой раз')->value('no'),
            ]);

        $this->ask($question, function (Answer $answer) {
            // Detect if button was clicked:
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue(); // will be either 'yes' or 'no'
                $selectedText = $answer->getText(); // will be either 'Of course' or 'Hell no!'

                if ($selectedValue == "promo_info") {
                    $this->promoInfo();
                }

                if ($selectedValue == "yes") {
                    $this->askFirstname();
                }

                if ($selectedValue == "no") {
                    $this->say("Хорошего дня!");
                }
            }
        });
    }

    public function promoInfo()
    {

        $promo = Promotion::find($this->data);
        $coords = explode(",", $promo->location_coords);
        $location_attachment = new Location($coords[0], $coords[1], [
            'custom_payload' => true,
        ]);
        $attachment = new Image($promo->promo_image_url);

        $message1 = OutgoingMessage::create("*" . $promo->title . "*\n_" . $promo->description . "_\n*Наш адрес*:" . $promo->location_address . "\n*Координаты акции*:")
            ->withAttachment($attachment);

        $message2 = OutgoingMessage::create("Акция проходит тут:")
            ->withAttachment($location_attachment);

        // Reply message object
        $this->bot->reply($message1, ["parse_mode" => "Markdown"]);
        $this->bot->reply($message2, ["parse_mode" => "Markdown"]);

        $question = Question::create('Так что на счет участия?')
            ->addButtons([
                Button::create('Поехали')->value('yes'),
                Button::create('Нет, в другой раз')->value('no'),
            ]);

        $this->ask($question, function (Answer $answer) {
            // Detect if button was clicked:
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();

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

                $this->say('Отлично, приятно познакомится ' . $this->user->fio_from_request);

                $message = Question::create("Продолжим дальше?")
                    ->addButtons([
                        Button::create("Далее")->value("next"),
                        Button::create("Позже")->value("stop"),
                    ]);


                $this->ask($message, function (Answer $answer) {
                    if ($answer->isInteractiveMessageReply()) {
                        if ($answer->getValue() == "next") {
                            $this->askPhone1();
                        }
                    }
                });


            });
        } else
            $this->askPhone1();
    }

    public function askPhone1()
    {
        if ($this->user->phone == null) {
            $question = Question::create('Скажие мне свой телефонный номер')
                ->fallback('Спасибо что пообщался со мной:)!')
                ->addButtons([
                    Button::create("Отправить мой номер")->value("send"),
                    Button::create("Ввести мой номер")->value("next"),
                ]);

            $this->ask($question, function (Answer $answer) {
                $this->user->phone = $answer->getText();
                $this->user->save();

                if ($answer->isInteractiveMessageReply()) {
                    if ($answer->getValue() == "send") {
                        $this->user->phone = $this->sendRequest("sendMessage",
                            [
                                "text" => "Подтвердить отправку телефона",
                                'reply_markup' => json_encode([
                                    'inline_keyboard' => [
                                        ['text' => 'Далее', 'request_contact' => "true"],
                                    ]
                                ]),
                                "parse_mode" => "Markdown"
                            ]);
                        $this->user->save();
                        $this->askSex();
                    }

                    if ($answer->getValue() == "next") {
                        $this->askPhone2();
                    }

                }


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
                    Button::create("\xF0\x9F\x91\xA6Парень")->value('man'),
                    Button::create("\xF0\x9F\x91\xA7Девушка")->value('woman'),
                ]);

            $this->ask($question, function (Answer $answer) {
                // Detect if button was clicked:
                if ($answer->isInteractiveMessageReply()) {

                    $this->user->sex = $answer->getValue() == "man" ? 0 : 1;
                    $this->user->save();

                    $message = Question::create("Продолжим дальше?:")
                        ->addButtons([
                            Button::create("Далее")->value("next"),
                            Button::create("Позже")->value("stop"),
                        ]);


                    $this->ask($message, function (Answer $answer) {
                        if ($answer->isInteractiveMessageReply()) {
                            if ($answer->getValue() == "next") {
                                $this->askBirthday();
                            }
                        }
                    });

                }
            });
        } else

            $this->askBirthday();


    }

    public function askBirthday()
    {
        if ($this->user->birthday == null) {
            $question = Question::create('Последний вопрос - дата твоего рождения:')
                ->fallback('Спасибо что пообщался со мной:)!');

            $this->ask($question, function (Answer $answer) {
                $this->user->birthday = $answer->getText();
                $this->user->save();

                $message = Question::create("Продолжим дальше?")
                    ->addButtons([
                        Button::create("Далее")->value("next"),
                        Button::create("Позже")->value("stop"),
                    ]);


                $this->ask($message, function (Answer $answer) {
                    if ($answer->isInteractiveMessageReply()) {
                        if ($answer->getValue() == "next") {
                            $this->askCity();
                        }
                    }
                });


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


        $promo = Promotion::find(intval($this->data));

        if ($promo->current_activation_count < $promo->activation_count) {

            if ($promo->immediately_activate == 1) {
                $this->user->referral_bonus_count += $promo->refferal_bonus;
                $this->bot->reply($promo->activation_text);

                $this->user->promos()->attach($promo->id);

                $promo->current_activation_count += 1;
                $promo->save();

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

            $attachment = new Image("https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://t.me/" . env("APP_BOT_NAME") . "?start=$code");

            // Build message object
            $message = OutgoingMessage::create('_Код для получения приза по акции_')
                ->withAttachment($attachment);

            // Reply message object
            $this->bot->reply($message, ["parse_mode" => "Markdown"]);
        }

    }

    public function askPhone2()
    {

        $question = Question::create('Введите свой телефон по формату *+38(0XX)XXX-XX-XX*')
            ->fallback('Спасибо что пообщался со мной:)!');

        $this->ask($question, function (Answer $answer) {

            $message = Question::create("Продолжим дальше?")
                ->addButtons([
                    Button::create("Далее")->value("next"),
                    Button::create("Позже")->value("stop"),
                ]);


            $this->ask($message, function (Answer $answer) {
                if ($answer->isInteractiveMessageReply()) {
                    if ($answer->getValue() == "next") {
                        $this->askSex();
                    }
                }
            });
        });


    }


}
