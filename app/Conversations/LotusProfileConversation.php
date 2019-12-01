<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;

use App\Promotion;
use App\User;
use App\UserHasPromo;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class LotusProfileConversation extends Conversation
{
    use CustomConversation;

    protected $data;
    protected $bot;

    public function __construct($bot, $data)
    {
        $this->bot = $bot;
        $this->data = $data;
    }

    public function run()
    {
        $this->model_name = null;
        $this->height = null;
        $this->weight = null;
        $this->breast_volume = null;
        $this->sex = null;
        $this->waist = null;
        $this->hips = null;
        $this->model_school_education = null;
        $this->about = null;
        $this->hobby = null;
        $this->education = null;
        $this->wish_learn = null;

        $telegramUser = $this->bot->getUser();
        $id = $telegramUser->getId();

        $this->user = \App\User::where("telegram_chat_id", $id)
            ->first();


        try {
            $this->askForStartPromo();
        } catch (\Exception $e) {
            $this->bot->reply($e);
        }

    }

    public function askForStartPromo()
    {
        $question = Question::create('Анкета участников модельного агентства')
            ->fallback('Ничего страшного, в следующий раз получится!')
            ->addButtons([
                Button::create('Подробности')->value('promo_info'),
                Button::create('Заполнить')->value('yes'),
                Button::create('Нет, в другой раз')->value('no'),
            ]);


        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();

                if ($selectedValue == "promo_info") {
                    $this->promoInfo();
                }

                if ($selectedValue == "yes") {
                    $this->conversationMenu("Начнем-с...");
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

        $message2 = OutgoingMessage::create("Мы находимся тут:")
            ->withAttachment($location_attachment);

        // Reply message object
        $this->bot->reply($message1, ["parse_mode" => "Markdown"]);
        $this->bot->reply($message2, ["parse_mode" => "Markdown"]);

        $question = Question::create('Так что на счет анкеты?')
            ->addButtons([
                Button::create('Поехали')->value('yes'),
                Button::create('Нет, в другой раз')->value('no'),
            ]);

        $this->ask($question, function (Answer $answer) {
            // Detect if button was clicked:
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();

                if ($selectedValue == "yes") {
                    $this->conversationMenu("Начнем-с...");
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
        if ($this->model_name == null) {
            $question = Question::create('Как тебя зовут?')
                ->fallback('Спасибо что пообщался со мной:)!');

            $this->ask($question, function (Answer $answer) {
                $this->model_name = $answer->getText();
                $this->user->fio_from_request = $answer->getText();
                $this->user->save();

                $this->askPhone();
            });
        } else
            $this->askPhone();
    }

    public function askPhone()
    {
        if ($this->user->phone == null) {
            $question = Question::create('Ваш контактный телефон')
                ->fallback('Спасибо что пообщался со мной:)!');

            $this->ask($question, function (Answer $answer) {

                $vowels = array("(", ")", "-", " ");
                $tmp_phone = $answer->getText();

                Log::info($tmp_phone);

                $tmp_phone = str_replace($vowels, "", $tmp_phone);

                Log::info($tmp_phone);

                if (!strpos($tmp_phone, "+38"))
                    $tmp_phone = "+38" . $tmp_phone;

                $tmp_user = User::where("phone", $tmp_phone)->first();

                $pattern = "/^\+380\d{3}\d{3}\d{2}\d{2}$/";
                $string = $tmp_user;

                if (!preg_match($pattern, $string)) {

                    $this->bot->reply("Номер введен не верно...\n");
                    $this->askPhone();
                    return;
                } else {

                    if ($tmp_user == null) {

                        $this->user->phone = $tmp_phone;
                        $this->user->save();


                    } else
                        $this->bot->reply("Пользователь с таким номером уже и так наш друг:)\n");

                }

                $this->askSex();
            });
        } else
            $this->askSex();

    }


    public function askSex()
    {
        if ($this->sex == null) {
            $question = Question::create('Ваш пол?')
                ->fallback('Спасибо что пообщался со мной:)!')
                ->addButtons([
                    Button::create("\xF0\x9F\x91\xA6Парень")->value('man'),
                    Button::create("\xF0\x9F\x91\xA7Девушка")->value('woman'),
                ]);

            $this->ask($question, function (Answer $answer) {
                // Detect if button was clicked:
                if ($answer->isInteractiveMessageReply()) {
                    $this->sex = $answer->getValue() == "man" ? 0 : 1;
                    $this->askAge();
                }
            });
        } else
            $this->askAge();


    }

    //рост

    public function askAge()
    {
        if ($this->user->age == null) {
            $question = Question::create('Сколько тебе лет?')
                ->fallback('Спасибо что пообщался со мной:)!');

            $this->ask($question, function (Answer $answer) {
                $this->user->age = intval($answer->getText()) ?? 18;
                $this->user->save();

                $this->askHeight();

            });
        } else
            $this->askHeight();
    }

    //вес

    public function askHeight()
    {

        if ($this->height == null) {
            $question = Question::create('Ваш рост:')
                ->fallback('Спасибо что пообщался со мной:)!');

            $this->ask($question, function (Answer $answer) {
                $this->height = $answer->getText();


                $this->askWeight();

            });
        } else
            $this->askWeight();
    }

    //объем груди

    public function askWeight()
    {
        if ($this->weight == null) {
            $question = Question::create('Ваш вес:')
                ->fallback('Спасибо что пообщался со мной:)!');

            $this->ask($question, function (Answer $answer) {
                $this->weight = $answer->getText();


                $this->askBreastVolume();

            });
        } else
            $this->askBreastVolume();
    }

    //объем талии

    public function askBreastVolume()
    {
        if ($this->breast_volume == null && $this->sex == 1) {
            $question = Question::create('Ваш объем груди:')
                ->fallback('Спасибо что пообщался со мной:)!');

            $this->ask($question, function (Answer $answer) {
                $this->breast_volume = $answer->getText();
                $this->askWaist();

            });
        } else
            $this->askWaist();
    }

    //объем бёдер

    public function askWaist()
    {
        if ($this->waist == null && $this->sex == 1) {
            $question = Question::create('Ваш объем талии:')
                ->fallback('Спасибо что пообщался со мной:)!');

            $this->ask($question, function (Answer $answer) {
                $this->waist = $answer->getText();
                $this->askHips();

            });
        } else
            $this->askHips();
    }

    //обучались в модельной школе?

    public function askHips()
    {
        if ($this->hips == null && $this->sex == 1) {
            $question = Question::create('Ваш объем бёдер:')
                ->fallback('Спасибо что пообщался со мной:)!');

            $this->ask($question, function (Answer $answer) {
                $this->hips = $answer->getText();
                $this->askModelSchool();

            });
        } else
            $this->askModelSchool();
    }

    //откуда узнали о нашем модельном агенстве

    public function askModelSchool()
    {
        if ($this->model_school_education == null) {
            $question = Question::create('Обучались в модельной школе?')
                ->fallback('Спасибо что пообщался со мной:)!')
                ->addButtons([
                    Button::create("\xE2\x9E\x95Да")->value('yes'),
                    Button::create("\xE2\x9D\x8CНет")->value('no'),
                ]);

            $this->ask($question, function (Answer $answer) {
                // Detect if button was clicked:
                if ($answer->isInteractiveMessageReply()) {

                    $this->model_school_education = $answer->getValue() == "yes" ? 1 : 0;

                    $this->askAboutUs();
                }
            });
        } else
            $this->askAboutUs();
    }

    //ваше хобби

    public function askAboutUs()
    {
        if ($this->about == null) {
            $question = Question::create('Откуда узнали о нашем модельном агенстве?')
                ->fallback('Спасибо что пообщался со мной:)!');

            $this->ask($question, function (Answer $answer) {
                $this->about = $answer->getText();
                $this->askHobby();

            });
        } else
            $this->askHobby();
    }

    //ваше образование

    public function askHobby()
    {
        if ($this->hobby == null) {
            $question = Question::create('Ваше хобби?')
                ->fallback('Спасибо что пообщался со мной:)!');

            $this->ask($question, function (Answer $answer) {
                $this->hobby = $answer->getText();
                $this->askEducation();

            });
        } else
            $this->askEducation();
    }

    //желание обучаться

    public function askEducation()
    {
        if ($this->education == null) {
            $question = Question::create('Ваше образование?')
                ->fallback('Спасибо что пообщался со мной:)!');

            $this->ask($question, function (Answer $answer) {
                $this->education = $answer->getText();
                $this->askWishLearn();

            });
        } else
            $this->askWishLearn();
    }

    public function askWishLearn()
    {
        if ($this->wish_learn == null) {
            $question = Question::create('Хотели бы обучаться у нас?')
                ->fallback('Спасибо что пообщался со мной:)!')
                ->addButtons([
                    Button::create("\xE2\x9E\x95Да")->value('yes'),
                    Button::create("\xE2\x9D\x8CНет")->value('no'),
                ]);

            $this->ask($question, function (Answer $answer) {
                // Detect if button was clicked:
                if ($answer->isInteractiveMessageReply()) {

                    $this->wish_learn = $answer->getValue() == "yes" ? 1 : 0;

                    $this->saveData();
                }
            });
        } else
            $this->saveData();
    }

    public function saveData()
    {


        $this->mainMenu("Отлично! Вы справились!");

        Telegram::sendMessage([
            'chat_id' => "-1001176319167",
            'parse_mode' => 'Markdown',
            'text' => "Новая анкета:\n"
                . "*Ф.И.О.*:" . ($this->model_name ?? 'Не указано') . "\n"
                . "*Возраст:*" . ($this->user->age ?? 'Не указано') . "\n"
                . "*Телефон:*" . ($this->user->phone ?? 'Не указано') . "\n"
                . "*Пол:*" . ($this->sex == 0 ? "Парень" : "Девушка") . "\n"
                . "*Рост:*" . ($this->height ?? 'Не указано') . "\n"
                . "*Вес:*" . ($this->weight ?? 'Не указано') . "\n"
                . "*Объем груди:*" . ($this->breast_volume ?? 'Не указано') . "\n"
                . "*Объем талии:*" . ($this->waist ?? 'Не указано') . "\n"
                . "*Объем бёдер:*" . ($this->hips ?? 'Не указано') . "\n"
                . "*Обучался ранее:*" . ($this->model_school_education == 1 ? "Да" : "Нет") . "\n"
                . "*Желает обучаться:*" . ($this->wish_learn == 1 ? "Да" : "Нет") . "\n"
                . "*Откуда узнал:*" . ($this->about ?? 'Не указано') . "\n"
                . "*Образование:*" . ($this->education ?? 'Не указано') . "\n"
            ,
            'disable_notification' => 'true'
        ]);

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
            $message = OutgoingMessage::create('_Код для получения бонуса по акции_')
                ->withAttachment($attachment);

            // Reply message object
            $this->bot->reply($message, ["parse_mode" => "Markdown"]);
        }

    }


}
