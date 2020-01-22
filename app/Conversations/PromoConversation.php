<?php

namespace App\Conversations;

use App\Classes\CustomBotMenu;
use App\Events\ActivateUserEvent;
use App\Promotion;
use App\User;
use App\UserHasPromo;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PromoConversation extends Conversation
{
    use CustomBotMenu;

    protected $data;


    public function __construct($bot, $data)
    {
        $this->setBot($bot);
        $this->data = $data;
    }

    public function run()
    {

        $on_promo = $this->getUser(["promos"])->promos()
            ->where("promotion_id", "=", intval($this->data))
            ->first();

        if ($on_promo) {
            $this->reply('Акция уже была пройдена ранее!');
            return;
        }

        try {
            $this->askForStartPromo();
        } catch (\Exception $e) {
            Log::error(get_class($this));
            $this->mainMenu(__("messages.menu_title_1"));
        }

    }

    public function askForStartPromo()
    {
        $question = Question::create('Желаете поучаствовать в акции?')
            ->fallback('Ничего страшного, в следующий раз получится!')
            ->addButtons([
                Button::create('Детали акции')->value('promo_info'),
                Button::create('Участвовать в акции')->value('yes'),
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

        if ($promo) {
            $coords = explode(",", $promo->location_coords);
            $this->sendPhoto("*" . $promo->title . "*\n_" . $promo->description . "_\n*Наш адрес*:" . $promo->location_address . "\n*Координаты акции*:", $promo->promo_image_url);
            $this->sendLocation($coords[0], $coords[1]);
        }

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
                    $this->reply("Хорошего дня!");
                }
            }
        });

    }

    public function askFirstname()
    {
        if ($this->getUser()->fio_from_request != "") {
            $this->askPhone();
            return;
        }
        $question = Question::create('Как тебя зовут?')
            ->fallback('Спасибо что пообщался со мной:)!');

        $this->ask($question, function (Answer $answer) {
            $user = $this->getUser();
            $user->fio_from_request = $answer->getText();
            $user->save();

            $this->askPhone();
        });

    }

    public function askPhone()
    {
        if ($this->getUser()->phone != null) {
            $this->askSex();
            return;
        }

        $question = Question::create('Скажие мне свой номер телефона')
            ->fallback('Спасибо что пообщались со мной:)!');

        $this->ask($question, function (Answer $answer) {

            $vowels = array("(", ")", "-", " ");

            $tmp_phone = str_replace($vowels, "", $answer->getText());

            $tmp_phone = strpos($tmp_phone, "+38") === false ?
                "+38" . $tmp_phone :
                $tmp_phone;

            $pattern = "/^\+380\d{3}\d{2}\d{2}\d{2}$/";

            if (preg_match($pattern, $tmp_phone) == 0) {

                $this->reply("Номер введен не верно...\n");
                $this->askPhone();
                return;
            } else {

                $tmp_user = User::where("phone", $tmp_phone)->first();

                if ($tmp_user == null) {
                    $user = $this->getUser();
                    $user->phone = $tmp_phone;
                    $user->save();

                } else {
                    $this->reply("Пользователь с таким номером уже и так наш друг:)\n");
                    $this->askPhone();
                    return;
                }

            }

            $this->askSex();
        });


    }

    public function askSex()
    {
        if ($this->getUser()->sex != null) {
            $this->askBirthday();
            return;
        }

        $question = Question::create('А какого вы пола?')
            ->fallback('Спасибо что пообщались со мной:)!')
            ->addButtons([
                Button::create("\xF0\x9F\x91\xA6Мужской")->value('man'),
                Button::create("\xF0\x9F\x91\xA7Женский")->value('woman'),
            ]);

        $this->ask($question, function (Answer $answer) {
            // Detect if button was clicked:
            if ($answer->isInteractiveMessageReply()) {
                $user = $this->getUser();
                $user->sex = $answer->getValue() == "man" ? 0 : 1;
                $user->save();

                $this->askBirthday();
            }
        });


    }

    public function askBirthday()
    {
        if ($this->getUser()->birthday != null) {
            $this->askCity();
            return;
        }

        $question = Question::create('Следующий вопрос - дата вашего рождения:')
            ->fallback('Спасибо что пообщались со мной:)!');

        $this->ask($question, function (Answer $answer) {
            $user = $this->getUser();
            $user->birthday = $answer->getText();
            $user->save();
            $this->askCity();

        });


    }

    public function askCity()
    {
        if ($this->getUser()->address != null) {
            $this->saveData();
            return;
        }

        $question = Question::create('Из какого вы города?')
            ->fallback('Спасибо что пообщались со мной:)!');

        $this->ask($question, function (Answer $answer) {
            $user = $this->getUser();
            $user->address = $answer->getText();
            $user->save();

            $this->saveData();
        });

    }

    public function saveData()
    {

        $this->mainMenu(__("messages.menu_title_4"));

        $promo = Promotion::find(intval($this->data));

        if ($promo->current_activation_count < $promo->activation_count) {

            if ($promo->immediately_activate == 1) {
                $user = $this->getUser();
                $user->referral_bonus_count += $promo->refferal_bonus;
                $this->reply($promo->activation_text);

                $user->promos()->attach($promo->id);
                $user->updated_at = Carbon::now();
                $user->save();

                event(new ActivateUserEvent($user));

                $promo->current_activation_count += 1;
                $promo->save();

            }
        }


        if ($promo->immediately_activate == 0) {
            $this->reply("Спасибо! Получите свои бонусы у нашего сотрудника по этому QR-коду.");

            $tmp_id = $this->getUser()->telegram_chat_id;
            while (strlen($tmp_id) < 10)
                $tmp_id = "0" . $tmp_id;

            $tmp_promo_id = $this->data;
            while (strlen($tmp_promo_id) < 10)
                $tmp_promo_id = "0" . $tmp_promo_id;

            $code = base64_encode("003" . $tmp_id . $tmp_promo_id);

            $this->sendPhoto('_Код для получения приза по акции_',
                env("QR_URL") . "https://t.me/" . env("APP_BOT_NAME") . "?start=$code");

        }

    }


}
