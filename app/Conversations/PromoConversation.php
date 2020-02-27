<?php

namespace App\Conversations;

use App\Classes\CustomBotMenu;
use App\Events\ActivateUserEvent;
use App\Models\SkidkaServiceModels\Promotion;
use App\User;
use App\Models\SkidkaServiceModels\UserHasPromo;
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
            $this->reply(__("messages.ask_promotions_error_1"));
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
        $question = Question::create(__("messages.ask_for_start_promo"))
            ->fallback(__("messages.ask_fallback"))
            ->addButtons([
                Button::create(__("messages.start_promo_btn_1"))->value('promo_info'),
                Button::create(__("messages.start_promo_btn_2"))->value('yes'),
                Button::create(__("messages.start_promo_btn_3"))->value('no'),
            ]);


        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();

                if ($selectedValue == "promo_info") {
                    $this->promoInfo();
                }

                if ($selectedValue == "yes") {
                    $this->conversationMenu(__("messages.menu_title_2"));
                    $this->askFirstname();
                }

                if ($selectedValue == "no") {
                    $this->say(__("messages.message_1"));
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

        $question = Question::create(__('messages.ask_promo_profile'))
            ->addButtons([
                Button::create(__("messages.ask_promo_btn_1"))->value('yes'),
                Button::create(__("messages.ask_promo_btn_2"))->value('no'),
            ]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();

                if ($selectedValue == "yes") {
                    $this->askFirstname();
                }

                if ($selectedValue == "no") {
                    $this->reply(__("messages.message_1"));
                }
            }
        });

    }

    public function askFirstname()
    {
        if (!is_null($this->getUser()->fio_from_request) && strlen(trim($this->getUser()->fio_from_request)) > 0) {
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

            if (strlen($tmp_phone) > 13) {
                $this->reply(__("messages.ask_phone_error_1"));
                $this->askPhone();
                return;
            }

            $pattern = "/^\+380\d{3}\d{2}\d{2}\d{2}$/";

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

        $question = Question::create(__("messages.ask_sex"))
            ->fallback(__("messages.ask_fallback"))
            ->addButtons([
                Button::create(__("messages.ask_sex_btn_1"))->value('man'),
                Button::create(__("messages.ask_sex_btn_2"))->value('woman'),
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
            $this->reply(__("messages.message_2"));

            $tmp_id = $this->getUser()->telegram_chat_id;
            while (strlen($tmp_id) < 10)
                $tmp_id = "0" . $tmp_id;

            $tmp_promo_id = $this->data;
            while (strlen($tmp_promo_id) < 10)
                $tmp_promo_id = "0" . $tmp_promo_id;

            $code = base64_encode("003" . $tmp_id . $tmp_promo_id);

            $this->sendPhoto(__("messages.message_3"),
                env("QR_URL") . "https://t.me/" . env("APP_BOT_NAME") . "?start=$code");

        }

    }


}
