<?php

namespace App\Conversations;

use App\Achievement;
use App\CashbackHistory;
use App\Classes\CustomBotMenu;
use App\Enums\AchievementTriggers;
use App\Event;
use App\Events\AchievementEvent;
use App\Events\NetworkLevelRecounterEvent;
use App\Promotion;
use App\RefferalsHistory;
use App\User;
use App\UserHasAchievement;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use Carbon\Carbon;
use DateTime;
use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class StartDataConversation extends Conversation
{
    use CustomBotMenu;

    protected $data;

    public function __construct($bot, $data)
    {
        $this->setBot($bot);
        $this->data = $data;
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        try {
            $this->startWithData();
        } catch (\Exception $e) {
            Log::error(get_class($this) . " " . $e->getMessage() . " " . $e->getLine());
            $this->fallbackMenu("Добрый день!Приветствуем вас в нашем акционном боте! Сейчас у нас технические работы.\n");
        }
    }

    /**
     * First question
     */
    public function startWithData()
    {
        $pattern = "/([0-9]{3})([0-9]{10})([0-9]{10})/";
        $string = base64_decode($this->data);

        //$this->say("Все данные:" . $string);

        preg_match_all($pattern, $string, $matches);

        $tmp_dev_id = (string)env("DEVELOPER_ID");
        while (strlen($tmp_dev_id) < 10)
            $tmp_dev_id = "0" . $tmp_dev_id;

        /*  $this->code = count($matches[1]) > 0 ? $matches[1][0] : env("CUSTOME_CODE");
          $this->request_user_id = count($matches[2]) > 0 ? $matches[2][0] : $tmp_dev_id;
          $this->promo_id = count($matches[3]) > 0 ? $matches[3][0] : env("CUSTOME_PROMO");*/

        //$this->say("Данные после компиляции:" . print_r($matches,true));

        $this->code = $matches[1][0] ?? env("CUSTOME_CODE");
        $this->request_user_id = $matches[2][0] ?? $tmp_dev_id;
        $this->promo_id = $this->ach_id = $matches[3][0] ?? env("CUSTOME_PROMO");


        $this->createNewUser();

        $canBeRefferal = true;

        if ($this->getUser()->is_admin == 1) {
            if ($this->code == "002") {
                $this->activatePayment();
                $canBeRefferal = false;
            }

            if ($this->code == "003") {
                $this->activatePromo();
                $canBeRefferal = false;
            }


            if ($this->code == "012") {
                $this->activateAchievement();
                $canBeRefferal = false;
            }

        }

        if ($canBeRefferal) {
            $this->activateRefferal();
            $this->mainMenu('Добрый день! Приветствуем вас в нашем акционном боте! Мы рады, что Вы присоединились к нам. Все акции будут активны с 1 февраля!');

            if ($this->code == "100" && intval($this->promo_id) != 0) {
                $this->openEvent();
                return;
            }

            if (intval($this->promo_id) != 0) {
                $this->openPromo();
            }
        }


    }

    protected function activatePayment()
    {
        if ($this->getUser()->is_admin == 0)
            return;

        $keyboard = [];

        $companies = $this->getUser(["companies", "promos"])->companies;

        foreach ($companies as $company)
            array_push($keyboard, [
                ["text" => $company->title, "callback_data" => "/payment " . $this->request_user_id . " " . $company->id]
            ]);

        if (count($keyboard) == 0) {
            $this->reply("Вы не добавлены не в одну компанию и не можете проводить процесс списания.");
            return;
        }

        $this->sendMessage("Диалог управления средствами\nВыберите вашу компанию:", $keyboard);


    }

    protected function activatePromo()
    {

        if ($this->getUser()->is_admin == 0)
            return;

        $promo = Promotion::find(intval($this->promo_id));

        $remote_user = User::with(["promos"])
            ->where("telegram_chat_id", intval($this->request_user_id))
            ->first();

        $on_promo = $remote_user->promos()
            ->where("promotion_id", "=", $promo->id)//promotion_id
            ->first();

        if ($on_promo) {
            $this->reply('Приз по акции уже был активирован ранее');
            return;
        }

        if ($on_promo == null && $promo->current_activation_count < $promo->activation_count) {
            $remote_user->promos()->attach($promo->id);

            $promo->current_activation_count += 1;
            $promo->save();

            $remote_user->referral_bonus_count += $promo->refferal_bonus;

            $remote_user->updated_at = Carbon::now();
            $remote_user->save();

            $this->sendMessageToChat($remote_user->telegram_chat_id,
                $promo->activation_text);

            $promoTitle = $promo->title;
            $promoDescription = $promo->description;
            $this->reply("Приз по акции `$promoTitle` успешно активирован. Описание:\n$promoDescription");


            event(new AchievementEvent(AchievementTriggers::MaxReferralBonusCount, 10, $remote_user));
            event(new AchievementEvent(AchievementTriggers::PromosActivationSequence, 1, $remote_user));
        }


        if ($promo->current_activation_count == $promo->activation_count) {
            $this->reply('Больше нет призов по акции, нет возможности выдать приз пользователю!');
            $this->sendMessageToChat($remote_user->telegram_chat_id,
                "Больше нет призов по акции, вы не успели(");
        }

        $remote_user->activated = 1;
        $remote_user->updated_at = Carbon::now();
        $remote_user->save();

        $ref = RefferalsHistory::where("user_recipient_id", $remote_user->id)->first();
        if ($ref == null)
            return;

        if ($ref->activated == 1)
            return;


        $ref->activated = 1;
        $ref->save();


        $sender_user = User::where("id", $ref->user_sender_id)->first();
        $sender_user->referral_bonus_count += env("REFERRAL_BONUS");
        $sender_user->save();

        event(new NetworkLevelRecounterEvent($sender_user->id));
        event(new AchievementEvent(AchievementTriggers::ReferralCount, 1, $sender_user));
        event(new AchievementEvent(AchievementTriggers::MaxReferralBonusCount, 1, $sender_user));

        $this->sendMessageToChat($remote_user->telegram_chat_id,
            "Вам начислено " . env("REFERRAL_BONUS") . " бонусов.");


    }

    protected function activateRefferal()
    {
        $sender_user = User::where("telegram_chat_id", intval($this->request_user_id))
            ->first();

        /* $this->bot->reply($this->request_user_id??"empty");
         $this->bot->reply($sender_user->id??"empty");*/

        if ($this->getUser()->id == $sender_user->id) {
            $this->reply("Вы перешли по собственной ссылке");
            return;
        }

        $on_refferal = RefferalsHistory::where("user_recipient_id", $this->getUser()->id)->first();

        if ($sender_user && !$on_refferal) {
            $sender_user->referrals_count += 1;
            $sender_user->save();

            $user = $this->getUser();
            $user->parent_id = $sender_user->id;
            $user->save();

            switch ($this->code) {
                case "004":
                    event(new AchievementEvent(AchievementTriggers::VKLinksActivationCount, 1, $user));
                    break;
                case "005":
                    event(new AchievementEvent(AchievementTriggers::FBLinksActivationCount, 1, $user));
                    break;
                case "006":
                    event(new AchievementEvent(AchievementTriggers::InstaLinksActivationCount, 1, $user));
                    break;
                case "007":
                case "008":
                case "009":
                case "010":
                case "011":
                    event(new AchievementEvent(AchievementTriggers::QRActivationCount, 1, $user));
                    break;
                default:
                    event(new AchievementEvent(AchievementTriggers::QRActivationCount, 1, $user));
                    break;
            }


            try {
                Telegram::sendMessage([
                    'chat_id' => $sender_user->telegram_chat_id,
                    'parse_mode' => 'Markdown',
                    'text' => "Пользователь " . (
                            $user->name ??
                            $user->fio_from_telegram ??
                            $user->email ??
                            $user->telegram_chat_id
                        ) . " перешел по вашей реферальной ссылке!",
                    'disable_notification' => 'false'
                ]);
            } catch (\Exception $e) {

            }

            RefferalsHistory::create([
                'user_sender_id' => $sender_user->id,
                'user_recipient_id' => $user->id,
                'activated' => 0,
            ]);

        }


    }

    protected function activateAchievement()
    {

        if ($this->getUser()->is_admin == 0)
            return;

        $achievement = Achievement::find(intval($this->ach_id));

        $remote_user = User::with(["achievements"])
            ->where("telegram_chat_id", intval($this->request_user_id))->first();

        $on_ach = UserHasAchievement::with(["achievement"])
            ->where("user_id", "=", $remote_user->id, 'and')
            ->where("achievement_id", "=", $achievement->id)
            ->first();

        if ($on_ach == null) {
            $this->reply(__("messages.ask_achievement_error_1"));
            return;
        }

        if ($on_ach->activated == 1) {
            $this->reply(__("messages.ask_achievement_error_2"));
            return;
        }


        $on_ach->activated = 1;
        $on_ach->save();

        $remote_user->updated_at = Carbon::now();
        $remote_user->activated = 1;
        $remote_user->save();

        event(new AchievementEvent(AchievementTriggers::AchievementActivatedCount, 1, $remote_user));

        $this->sendMessageToChat($remote_user->telegram_chat_id,
            "Достижение " . $on_ach->achievement->title . " успешно активировано!");

    }

    protected function openPromo()
    {

        $promo = Promotion::with(["users"])->find(intval($this->promo_id));

        $on_promo = $promo->onPromo($this->getChatId());

        if ($on_promo) {
            $this->reply(__("messages.ask_promotions_error_1"));
            return;
        }

        if (!$promo->isActive()) {
            $this->reply(__("messages.ask_promotions_error_2"));
            return;
        }

        $keyboard = [
            [
                ["text" => __("messages.start_con_btn_2"), "callback_data" => $promo->handler == null ? "/promotion " . $promo->id : $promo->handler . " " . $promo->id]
            ]
        ];
        $this->sendPhoto("*" . $promo->title . "*", $promo->promo_image_url, $keyboard);
    }

    protected function openEvent()
    {

        $event_id = $this->promo_id;

        $event = Event::find(intval($event_id));

        if (!$event->isActive()) {
            $this->reply(__("messages.ask_event_error_1"));
            return;
        }

        $this->sendPhoto("*" . $event->title . "*\n" . $event->description, $event->event_image_url);

    }
}

