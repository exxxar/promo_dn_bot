<?php

namespace App\Conversations;

use App\Classes\BaseBot;
use App\Classes\SkidkiBotMenu;
use BotMan\BotMan\Messages\Conversations\Conversation;

use App\Models\SkidkaServiceModels\Promotion;
use App\User;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class LotusProfileConversation extends Conversation
{
    use BaseBot;

    protected $about;
    protected $model_name;
    protected $height;
    protected $weight;
    protected $breast_volume;
    protected $sex;
    protected $waist;
    protected $hips;
    protected $model_school_education;
    protected $hobby;
    protected $education;
    protected $wish_learn;
    protected $data;
    protected $on_promo;

    public function __construct($bot, $data)
    {

        $this->setBot($bot);
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


        $this->on_promo = $this->getUser(["promos"])->promos()
            ->where("promotion_id", "=", intval($this->data))
            ->first();

        $promo = Promotion::where("id", intval($this->data))->first();

        if ($this->on_promo && $this->on_promo->pivot->user_activation_count <= $promo->user_can_activate_count) {
            $this->reply(__("messages.promo_message_3"));
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
        $question = Question::create(__("messages.lotus_model_profile"))
            ->fallback(__("messages.ask_fallback"))
            ->addButtons([
                Button::create(__("messages.start_promo_btn_1"))->value('promo_info'),
                Button::create(__("messages.start_promo_btn_2"))->value('yes'),
                Button::create(__("messages.start_promo_btn_3"))->value('no'),
            ]);

        Log::info("1 test data=".$this->data);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();

                if ($selectedValue == "promo_info") {
                    $this->promoInfo();
                    return;
                }

                if ($selectedValue == "yes") {
                    $this->conversationMenu(__("messages.menu_title_2"));
                    $this->askFirstname();
                    return;

                }

                if ($selectedValue == "no") {
                    $this->reply(__("messages.message_1"));
                    return;
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

        $question = Question::create(__("messages.ask_lotus_profile"))
            ->addButtons([
                Button::create(__("messages.ask_promo_btn_1"))->value('yes'),
                Button::create(__("messages.ask_promo_btn_2"))->value('no'),
            ]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();

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

    public function askFirstname()
    {
        if (!is_null($this->model_name)) {
            $this->askPhone();
            return;
        }

        $question = Question::create(__("messages.ask_name"))
            ->fallback(__("messages.ask_fallback"));

        $this->ask($question, function (Answer $answer) {
            $this->model_name = $answer->getText()??'';

            $user = $this->getUser();
            $user->fio_from_request = $this->model_name;
            $user->save();

            $this->askPhone();
        });

    }

    public function askPhone()
    {
        if (!is_null($this->getUser()->phone)&&strlen(trim($this->getUser()->phone))>0) {
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

            if (strlen($tmp_phone)>13){
                $this->reply(__("messages.ask_phone_error_1"));
                $this->askPhone();
                return;
            }

            $pattern = "/^\+380\d{3}\d{2}\d{2}\d{2}$/";

            if (preg_match($pattern, $tmp_phone) == 0) {
                $this->bot->reply(__("messages.ask_phone_error_1"));
                $this->askPhone();
                return;
            } else {

                $tmp_user = User::where("phone", $tmp_phone)->first();

                if ($tmp_user == null) {
                    $user = $this->getUser();
                    $user->phone = $tmp_phone;
                    $user->save();
                } else {
                    $this->bot->reply(__("messages.ask_phone_error_2"));
                    $this->askPhone();
                    return;
                }

            }

            $this->askSex();
        });


    }


    public function askSex()
    {
        if (!is_null($this->sex)) {
            $this->askAge();
            return;
        }

        $question = Question::create(__("messages.ask_sex"))
            ->fallback(__("messages.ask_fallback"))
            ->addButtons([
                Button::create(__("messages.ask_sex_btn_1"))->value('man'),
                Button::create(__("messages.ask_sex_btn_2"))->value('woman'),
            ]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->sex = $answer->getValue() == "man" ? 0 : 1;
                $this->askAge();
            }
        });

    }

    //рост

    public function askAge()
    {
        if (!is_null($this->getUser()->age )) {
            $this->askHeight();
            return;
        }
        $question = Question::create(__("messages.ask_age"))
            ->fallback(__("messages.ask_fallback"));

        $this->ask($question, function (Answer $answer) {
            $user = $this->getUser();
            $user->age = intval($answer->getText()) ?? 18;
            $user->save();
            $this->askHeight();
        });


    }


    public function askHeight()
    {
        if (!is_null($this->height)) {
            $this->askWeight();
            return;
        }

        $question = Question::create(__("messages.ask_height"))
            ->fallback(__("messages.ask_fallback"));

        $this->ask($question, function (Answer $answer) {
            $this->height = $answer->getText()??'';
            $this->askWeight();
        });

    }


    public function askWeight()
    {
        if (!is_null($this->weight)) {
            $this->askBreastVolume();
            return;
        }
        $question = Question::create(__("messages.ask_weight"))
            ->fallback(__("messages.ask_fallback"));

        $this->ask($question, function (Answer $answer) {
            $this->weight = $answer->getText()??'';
            $this->askBreastVolume();
        });
    }

    //объем талии

    public function askBreastVolume()
    {
        if (!is_null($this->breast_volume) || $this->sex == 0) {
            $this->askWaist();
            return;
        }
        $question = Question::create(__("messages.ask_breast_volume"))
            ->fallback(__("messages.ask_fallback"));

        $this->ask($question, function (Answer $answer) {
            $this->breast_volume = $answer->getText()??'';
            $this->askWaist();

        });

    }

    //объем бёдер

    public function askWaist()
    {
        if (!is_null($this->waist) || $this->sex == 0) {
            $this->askHips();
            return;
        }
        $question = Question::create(__("messages.ask_waist"))
            ->fallback(__("messages.ask_fallback"));

        $this->ask($question, function (Answer $answer) {
            $this->waist = $answer->getText()??'';
            $this->askHips();
        });


    }


    public function askHips()
    {
        if (!is_null($this->hips) || $this->sex == 0) {
            $this->askModelSchool();
            return;
        }
        $question = Question::create(__("messages.ask_hips"))
            ->fallback(__("messages.ask_fallback"));

        $this->ask($question, function (Answer $answer) {
            $this->hips = $answer->getText()??'';
            $this->askModelSchool();
        });

    }


    public function askModelSchool()
    {
        if (!is_null($this->model_school_education)) {
            $this->askAboutUs();
            return;
        }
        $question = Question::create(__("messages.ask_model_school_education"))
            ->fallback(__("messages.ask_fallback"))
            ->addButtons([
                Button::create(__("messages.ask_model_school_education_btn_1"))->value('yes'),
                Button::create(__("messages.ask_model_school_education_btn_2"))->value('no'),
            ]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->model_school_education = $answer->getValue() == "yes" ? 1 : 0;
                $this->askAboutUs();
            }
        });
    }


    public function askAboutUs()
    {
        if (is_null($this->about)) {
            $this->askHobby();
            return;
        }
        $question = Question::create(__("messages.ask_about"))
            ->fallback(__("messages.ask_fallback"));

        $this->ask($question, function (Answer $answer) {
            $this->about = $answer->getText()??'';
            $this->askHobby();
        });

    }

    //ваше образование

    public function askHobby()
    {
        if (!is_null($this->hobby) ) {
            $this->askEducation();
            return;
        }
        $question = Question::create(__("messages.ask_hobby"))
            ->fallback(__("messages.ask_fallback"));

        $this->ask($question, function (Answer $answer) {
            $this->hobby = $answer->getText()??'';
            $this->askEducation();
        });


    }

    //желание обучаться

    public function askEducation()
    {
        if (!is_null($this->education)) {
            $this->askWishLearn();
            return;
        }
        $question = Question::create(__("messages.ask_education"))
            ->fallback(__("messages.ask_fallback"));

        $this->ask($question, function (Answer $answer) {
            $this->education = $answer->getText()??'';
            $this->askWishLearn();
        });

    }

    public function askWishLearn()
    {
        if (!is_null($this->wish_learn)) {
            $this->saveData();
            return;
        }
        $question = Question::create(__("messages.ask_wish_learn"))
            ->fallback(__("messages.ask_fallback"))
            ->addButtons([
                Button::create(__("messages.ask_wish_learn_btn_1"))->value('yes'),
                Button::create(__('messages.ask_wish_learn_btn_2'))->value('no'),
            ]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->wish_learn = $answer->getValue() == "yes" ? 1 : 0;
                $this->saveData();
            }
        });


    }

    public function saveData()
    {


        $this->mainMenu(__("messages.menu_title_4"));

        Telegram::sendMessage([
            'chat_id' => env("LOTUS_MODEL_HUB_CHANNEL"),
            'parse_mode' => 'Markdown',
            'text' => "Новая анкета:\n"
                . "*Ф.И.О.*:" . ($this->model_name ?? __("messages.empty_data")) . "\n"
                . "*Возраст:*" . ($this->getUser()->age ?? __("messages.empty_data")) . "\n"
                . "*Телефон:*" . ($this->getUser()->phone ?? __("messages.empty_data")) . "\n"
                . "*Пол:*" . ($this->sex == 0 ?
                    __("messages.ask_sex_btn_1") :
                    __("messages.ask_sex_btn_2")) . "\n"
                . "*Рост:*" . ($this->height ?? __("messages.empty_data")) . "\n"
                . "*Вес:*" . ($this->weight ?? __("messages.empty_data")) . "\n"
                . "*Объем груди:*" . ($this->breast_volume ?? __("messages.empty_data")) . "\n"
                . "*Объем талии:*" . ($this->waist ?? __("messages.empty_data")) . "\n"
                . "*Объем бёдер:*" . ($this->hips ?? __("messages.empty_data")) . "\n"
                . "*Обучался ранее:*" . ($this->model_school_education == 1 ?
                    __("messages.ask_model_school_education_btn_1") :
                    __("messages.ask_model_school_education_btn_2")) . "\n"
                . "*Желает обучаться:*" . ($this->wish_learn == 1 ?
                    __("messages.ask_wish_learn_btn_1") :
                    __("messages.ask_wish_learn_btn_2")) . "\n"
                . "*Откуда узнал:*" . ($this->about ?? __("messages.empty_data")) . "\n"
                . "*Образование:*" . ($this->education ?? __("messages.empty_data")) . "\n"
            ,
            'disable_notification' => 'true'
        ]);

        $promo = Promotion::find(intval($this->data));

        if ($promo->current_activation_count < $promo->activation_count) {

            if ($promo->immediately_activate == 1) {

                $user = $this->getUser();
                $user->referral_bonus_count += $promo->refferal_bonus;
                $user->updated_at = Carbon::now();

                $this->reply($promo->activation_text);
                $user->promos()->attach($promo->id,[]);

                if (is_null($this->on_promo)) {
                    $user->promos()->attach($promo->id, ["user_activation_count" => 1]);
                } else {
                    if ($this->on_promo->pivot->user_activation_count <= $promo->user_can_activate_count) {
                        $this->on_promo->pivot->user_activation_count += 1;
                        $this->on_promo->pivot->save();

                        $this->reply("У вас осталось *" . ($promo->user_can_activate_count - $this->on_promo->pivot->user_activation_count) . "* активаций.");

                    }
                }

                $user->save();
                $promo->current_activation_count += 1;
                $promo->save();

            }
        }


        if ($promo->immediately_activate == 0) {
            $this->reply(__("messages.message_2"));

            $tmp_id = (string)$this->getUser()->telegram_chat_id;
            while (strlen($tmp_id) < 10)
                $tmp_id = "0" . $tmp_id;

            $tmp_promo_id = $this->data;
            while (strlen($tmp_promo_id) < 10)
                $tmp_promo_id = "0" . $tmp_promo_id;

            $code = base64_encode("003" . $tmp_id . $tmp_promo_id);

            $this->sendPhoto(__("messages.message_3"),
                env("QR_URL") . "https://t.me/" . env("APP_BOT_NAME") . "?start=$code"
            );

        }


    }


}
