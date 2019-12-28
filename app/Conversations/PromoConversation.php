<?php

namespace App\Conversations;

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
        $telegramUser = $this->bot->getUser();
        $id = $telegramUser->getId();

        $this->user = \App\User::with(["promos"])->where("telegram_chat_id", $id)
            ->first();

        $on_promo = $this->user->promos()
            ->where("promotion_id", "=", intval($this->data))
            ->first();

        if ($on_promo) {
            $this->bot->reply('Акция уже была пройдена ранее!');
            return;
        }

        $this->askForStartPromo();

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
        if ($this->user->fio_from_request != "") {
            $this->askPhone();
            return;
        }
        $question = Question::create('Как тебя зовут?')
            ->fallback('Спасибо что пообщался со мной:)!');

        $this->ask($question, function (Answer $answer) {
            $this->user->fio_from_request = $answer->getText();
            $this->user->save();

            $this->askPhone();
        });

    }

    public function askPhone()
    {
        if ($this->user->phone != null) {
            $this->askSex();
            return;
        }

        $question = Question::create('Скажие мне свой номер телефона')
            ->fallback('Спасибо что пообщались со мной:)!');

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

        $question = Question::create('А какого вы пола?')
            ->fallback('Спасибо что пообщались со мной:)!')
            ->addButtons([
                Button::create("\xF0\x9F\x91\xA6Мужской")->value('man'),
                Button::create("\xF0\x9F\x91\xA7Женский")->value('woman'),
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

        $question = Question::create('Следующий вопрос - дата вашего рождения:')
            ->fallback('Спасибо что пообщались со мной:)!');

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

        $question = Question::create('Из какого вы города?')
            ->fallback('Спасибо что пообщались со мной:)!');

        $this->ask($question, function (Answer $answer) {
            $this->user->address = $answer->getText();
            $this->user->save();

            $this->saveData();
        });

    }

    public function saveData()
    {


        $this->mainMenu("Отлично! Вы справились!");

        $promo = Promotion::find(intval($this->data));

        if ($promo->current_activation_count < $promo->activation_count) {

            if ($promo->immediately_activate == 1) {
                $this->user->referral_bonus_count += $promo->refferal_bonus;
                $this->bot->reply($promo->activation_text);

                $this->user->promos()->attach($promo->id);
                $this->user->updated_at = Carbon::now();
                $this->user->save();

                event(new ActivateUserEvent( $this->user));

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

            //$attachment = new Image(env("QR_URL")."https://t.me/" . env("APP_BOT_NAME") . "?start=$code");
            $attachment = new Image(env("APP_URL")."/image/?data=".base64_encode("https://t.me/" . env("APP_BOT_NAME") . "?start=$code"));

            // Build message object
            $message = OutgoingMessage::create('_Код для получения приза по акции_')
                ->withAttachment($attachment);

            // Reply message object
            $this->bot->reply($message, ["parse_mode" => "Markdown"]);
        }

    }


}
