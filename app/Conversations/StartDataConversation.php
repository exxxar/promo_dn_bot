<?php

namespace App\Conversations;

use App\CashbackHistory;
use App\Promotion;
use App\RefferalsHistory;
use App\User;
use App\UserHasPromo;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
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
            $this->fallbackMenu("Добрый день!Приветствуем вас в нашем акционном боте! Сейчас у нас технические работы.");
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
        $this->promo_id = $matches[3][0] ?? env("CUSTOME_PROMO");


        $telegramUser = $this->bot->getUser();
        $id = $telegramUser->getId();
        $this->user = User::with(["companies"])
            ->where("telegram_chat_id", $id)
            ->first();

        if ($this->user == null)
            $this->user = $this->createUser($telegramUser);

        $canBeRefferal = true;

        if ($this->user->is_admin == 1) {
            $this->mainMenuWithAdmin('Добрый день,ув. Администратор!');
            //$this->say("Вы администратор, входыне параметры:" . $this->code . " " . $this->request_user_id . " " . $this->promo_id);

            if ($this->code == "002") {
                $this->activatePayment();
                $canBeRefferal = false;
            }

            if ($this->code == "003") {
                $this->activatePromo();
                $canBeRefferal = false;
            }

        }

        if ($canBeRefferal) {
            $this->activateRefferal();
            $this->mainMenu('Добрый день! Приветствуем вас в нашем акционном боте! У нас вы сможете найти самые актуальные акции!');

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
                    ->where("promotion_id", "=", $promo->id)
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

                    $remote_user->save();

                    $this->bot->sendRequest("sendMessage", [
                        "chat_id" => $remote_user->telegram_chat_id,
                        "text" => $promo->activation_text
                    ]);
                }


                $ref = RefferalsHistory::where("user_recipient_id", $remote_user->id)->first();
                if ($ref) {
                    if ($ref->activated == 0) {
                        $ref->activated = 1;
                        $ref->save();

                        $sender_user = User::where("id", $ref->user_sender_id)->first();
                        $sender_user->referral_bonus_count += env("REFERRAL_BONUS");
                        $sender_user->save();

                        $this->bot->sendRequest("sendMessage", [
                            "chat_id" => $sender_user->telegram_chat_id,
                            "text" => "Вам начислено " . env("REFERRAL_BONUS") . " бонусов."
                        ]);

                        $this->bot->reply('Приз по акции успешно активирован');
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

        $on_refferal = RefferalsHistory::where("user_recipient_id", $this->user->id)->first();

        if ($sender_user && !$on_refferal) {
            $sender_user->referrals_count += 1;
            $sender_user->save();

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

    protected function openPromo()
    {

        $promo = Promotion::find(intval($this->promo_id));
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
                Button::create('Поехали')->value("/promotion " . $promo->id),
                Button::create('Нет, в другой раз')->value('stop'),
            ]);

        $this->bot->reply($question);

    }
}

