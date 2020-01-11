<?php

namespace App\Conversations;

use App\Achievement;
use App\CashbackHistory;
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
    use CustomConversation;


    protected $data;
    protected $bot;

    public function __construct($bot, $data)
    {
        $this->bot = $bot;
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
            Log::info($e->getMessage());
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


        $telegramUser = $this->bot->getUser();
        $id = $telegramUser->getId();
        $this->user = User::with(["companies", "promos"])
            ->where("telegram_chat_id", $id)
            ->first();

        if ($this->user == null)
            $this->user = $this->createUser($telegramUser);

        $canBeRefferal = true;

        if ($this->user->is_admin == 1) {
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
            $this->mainMenu('Добрый день! Приветствуем вас в нашем акционном боте! Мы рады, что Вы присоединились к нам. Все акции будут активны с 5 января!');

            if ($this->code=="100"&&intval($this->promo_id) != 0){
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
        if ($this->user->is_admin == 1) {

            $tmp = [];

            foreach ($this->user->companies as $company)
                array_push($tmp, Button::create($company->title)->value("/payment " . $this->request_user_id . " " . $company->id));

            if (count($tmp) > 0) {

                $message = Question::create("Диалог управления средствами\nВыберите вашу компанию:")
                    ->addButtons($tmp);

                $this->bot->reply($message);
            } else
                $this->bot->reply("Вы не добавлены не в одну компанию и не можете проводить процесс списания.");
        }




    }

    protected function activatePromo()
    {
        try {
            if ($this->user->is_admin == 1) {
                $promo = Promotion::find(intval($this->promo_id));

                $remote_user = User::with(["promos"])->where("telegram_chat_id", intval($this->request_user_id))->first();

                $on_promo = $remote_user->promos()
                    ->where("promotion_id", "=", $promo->id)//promotion_id
                    ->first();

                if ($on_promo) {
                    $this->bot->reply('Приз по акции уже был активирован ранее');
                    return;
                }

                if ($on_promo == null && $promo->current_activation_count < $promo->activation_count) {
                    $remote_user->promos()->attach($promo->id);

                    $promo->current_activation_count += 1;
                    $promo->save();

                    $remote_user->referral_bonus_count += $promo->refferal_bonus;

                    $remote_user->updated_at = Carbon::now();
                    $remote_user->save();


                    $this->bot->sendRequest("sendMessage", [
                        "chat_id" => $remote_user->telegram_chat_id,
                        "text" => $promo->activation_text
                    ]);

                    $this->bot->reply('Приз по акции успешно активирован');


                    event(new AchievementEvent(AchievementTriggers::MaxReferralBonusCount,10,$remote_user));
                    event(new AchievementEvent(AchievementTriggers::PromosActivationSequence,1,$remote_user));
                }


                if ($promo->current_activation_count == $promo->activation_count) {
                    $this->bot->reply('Больше нет призов по акции, нет возможности выдать приз пользователю!');
                    $this->bot->sendRequest("sendMessage", [
                        "chat_id" => $remote_user->telegram_chat_id,
                        "text" => "Больше нет призов по акции, вы не успели("
                    ]);
                }

                $remote_user->activated = 1;
                $remote_user->updated_at = Carbon::now();
                $remote_user->save();

                $ref = RefferalsHistory::where("user_recipient_id", $remote_user->id)->first();
                if ($ref) {
                    if ($ref->activated == 0) {
                        $ref->activated = 1;
                        $ref->save();



                        $sender_user = User::where("id", $ref->user_sender_id)->first();
                        $sender_user->referral_bonus_count += env("REFERRAL_BONUS");
                        $sender_user->save();

                        event(new NetworkLevelRecounterEvent($sender_user->id));
                        event(new AchievementEvent(AchievementTriggers::ReferralCount,1,$sender_user));
                        event(new AchievementEvent(AchievementTriggers::MaxReferralBonusCount,1,$sender_user));


                        $this->bot->sendRequest("sendMessage", [
                            "chat_id" => $sender_user->telegram_chat_id,
                            "text" => "Вам начислено " . env("REFERRAL_BONUS") . " бонусов."
                        ]);


                    }


                }


            }
        } catch (\Exception $e) {
            $this->bot->reply($e->getMessage() . " " . $e->getLine());
        }



    }

    protected function activateRefferal()
    {
        $sender_user = User::where("telegram_chat_id", intval($this->request_user_id))
            ->first();

       /* $this->bot->reply($this->request_user_id??"empty");
        $this->bot->reply($sender_user->id??"empty");*/

        if ($this->user->id == $sender_user->id) {
            $this->bot->reply("Вы перешли по собственной ссылке", ["parse_mode" => "Markdown"]);
            return;
        }

        $on_refferal = RefferalsHistory::where("user_recipient_id", $this->user->id)->first();

        if ($sender_user && !$on_refferal) {
            $sender_user->referrals_count += 1;
            $sender_user->save();

            $this->user->parent_id = $sender_user->id;
            $this->user->save();

            switch ($this->code){
                case "004": event(new AchievementEvent(AchievementTriggers::VKLinksActivationCount,1,$this->user)); break;
                case "005": event(new AchievementEvent(AchievementTriggers::FBLinksActivationCount,1,$this->user)); break;
                case "006": event(new AchievementEvent(AchievementTriggers::InstaLinksActivationCount,1,$this->user)); break;
                case "007":
                case "008":
                case "009":
                case "010":
                case "011":event(new AchievementEvent(AchievementTriggers::QRActivationCount,1,$this->user)); break;
                default:
                    event(new AchievementEvent(AchievementTriggers::QRActivationCount,1,$this->user));
                    break;
            }



            Telegram::sendMessage([
                'chat_id' => $sender_user->telegram_chat_id,
                'parse_mode' => 'Markdown',
                'text' => "Пользователь " . (
                        $this->user->name ??
                        $this->user->fio_from_telegram ??
                        $this->user->email ??
                        $this->user->telegram_chat_id
                    ) . " перешел по вашей реферальной ссылке!",
                'disable_notification' => 'false'
            ]);

            RefferalsHistory::create([
                'user_sender_id' => $sender_user->id,
                'user_recipient_id' => $this->user->id,
                'activated' => 0,
            ]);

        }


    }

    protected function activateAchievement()
    {
        try {
            if ($this->user->is_admin == 1) {
                $achievement = Achievement::find(intval($this->ach_id));

                $remote_user = User::with(["achievements"])
                    ->where("telegram_chat_id", intval($this->request_user_id))->first();

                $on_ach = UserHasAchievement::with(["achievement"])
                ->where("user_id","=",$remote_user->id,'and')
                    ->where("achievement_id","=",$achievement->id)
                    ->first();

                if ($on_ach==null) {
                    $this->bot->reply('Достижение не найдено');
                    return;
                }

                if ($on_ach->activated==1) {
                    $this->bot->reply('Приз за достижение уже был активирован ранее');
                    return;
                }

                if ($on_ach->activated == 0) {
                    $on_ach->activated=1;
                    $on_ach->save();

                    $remote_user->updated_at = Carbon::now();
                    $remote_user->activated = 1;
                    $remote_user->save();

                    event(new AchievementEvent(AchievementTriggers::AchievementActivatedCount,1,$remote_user));

                    $this->bot->sendRequest("sendMessage", [
                        "chat_id" => $remote_user->telegram_chat_id,
                        "text" => "Достижение ".$on_ach->achievement->title." успешно активировано!"
                    ]);
                }
            }
        } catch (\Exception $e) {
            $this->bot->reply($e->getMessage() . " " . $e->getLine());
        }
    }

    protected function openPromo()
    {

        $promo = Promotion::with(["users"])->find(intval($this->promo_id));

        $telegramUser = $this->bot->getUser();
        $id = $telegramUser->getId();

        $on_promo = $promo->users()->where('telegram_chat_id', "$id")->first();

        $time_0 = (date_timestamp_get(new DateTime($promo->start_at)));
        $time_1 = (date_timestamp_get(new DateTime($promo->end_at)));

        $time_2 = date_timestamp_get(now());

        if ($on_promo == null && $time_2 >= $time_0 && $time_2 < $time_1) {


            $attachment = new Image($promo->promo_image_url);
            $message = OutgoingMessage::create()
                ->withAttachment($attachment);
            $this->bot->reply($message,["parse_mode"=>"Markdown"]);

            $message = Question::create("*".$promo->title."*")
                ->addButtons([
                    Button::create("\xF0\x9F\x91\x89Подробнее")->value($promo->handler==null?"/promotion " . $promo->id:$promo->handler . " " . $promo->id)
                ]);

            $this->bot->reply($message,["parse_mode"=>"Markdown"]);
        }

        if ($on_promo) {
            $this->bot->reply('Акция уже была пройдена ранее!');
            return;
        }

        if ($time_2 > $time_1)
            $this->bot->reply('Акция уже подошла к концу!');
    }

    protected function openEvent()
    {

        $event_id = $this->promo_id;

        $event = Event::find(intval($event_id));

        $telegramUser = $this->bot->getUser();
        $id = $telegramUser->getId();


        $time_0 = (date_timestamp_get(new DateTime($event->start_at)));
        $time_1 = (date_timestamp_get(new DateTime($event->end_at)));

        $time_2 = date_timestamp_get(now());

        if ($time_2 >= $time_0 && $time_2 < $time_1) {


            $attachment = new Image($event->event_image_url);
            $message = OutgoingMessage::create("*" . $event->title . "*\n" . $event->description)
                ->withAttachment($attachment);

            $this->bot->reply($message,["parse_mode"=>"Markdown"]);
        }

        if ($time_2 > $time_1)
            $this->bot->reply('Мероприятие уже подошло к концу!');
    }
}

