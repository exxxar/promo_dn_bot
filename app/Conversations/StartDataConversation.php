<?php

namespace App\Conversations;

use App\CashbackHistory;
use App\Promotion;
use App\RefferalsHistory;
use App\User;
use App\UserHasPromo;
use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

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
        $this->startWithData();
    }

    /**
     * First question
     */
    public function startWithData()
    {

        $pattern = "/([0-9]{3})([0-9]{10})([0-9]{10})/";
        $string = base64_decode($this->data);
        preg_match_all($pattern, $string, $matches);

        $code = $matches[1][0];
        $user_id = $matches[2][0];
        $promo_id = $matches[3][0];


        $telegramUser = $this->bot->getUser();

        $id = $telegramUser->getId();

        $username = $telegramUser->getUsername();
        $lastName = $telegramUser->getLastName();
        $firstName = $telegramUser->getFirstName();

        $user = \App\User::where("telegram_chat_id", $id)
            ->first();


        if ($user == null) {

            $user = \App\User::create([
                'name' => $username,
                'email' => "$id@t.me",
                'password' => bcrypt($id),
                'fio_from_telegram' => "$firstName $lastName",
                'fio_from_request' => '',
                'phone' => '',
                'avatar_url' => '$telegramUser->getUserProfilePhotos()[0]->file_path',
                'address' => '',
                'sex' => 0,
                'age' => 18,
                'source' => "000",
                'telegram_chat_id' => $id,
                'referrals_count' => 0,
                'referral_bonus_count' => 10,
                'cashback_bonus_count' => 0,
                'is_admin' => false,
            ]);

            $this->bot->reply("Вы у нас первый раз? - Мы дарим вам <b>10</b> бонусным баллов!");
        }

        $promo = \App\Promotion::find($promo_id);

        switch ($code) {

            case "002":
                if ($user->is_admin == 1) {
                    $message = Question::create("Диалог списания средств:")
                        ->addButtons(
                            [
                                Button::create("Начать процесс оплаты")->value("/payment " . $user_id)
                            ]
                        );
                    $this->bot->reply($message);
                }

                $this->bot->reply('Спасибо, что пользуетесь нашей системой!)');
                break;
            case "003":
                if ($user->is_admin == 1) {
                    $promo = Promotion::find(intval($this->data));

                    $remote_user = User::where("", intval($user_id));

                    $on_promo = UserHasPromo::where("telegram_chat_id", "=", $remote_user->id)
                        ->where("promotion_id", "=", $promo->id)
                        ->first();

                    if ($on_promo == null && $promo->current_activation_count < $promo->activation_count) {
                        $remote_user->promos()->attach($promo->id);

                        $promo->current_activation_count += 1;
                        $promo->save();


                        $remote_user->referrals_count += 1;
                        $remote_user->referral_bonus_count += $promo->refferal_bonus;

                        $remote_user->save();

                        $this->bot->sendRequest("sendMessage",
                            ["chat_id" => $remote_user->telegram_chat_id, "text" => $promo->activation_tex]);
                    }

                    $ref = RefferalsHistory::where("user_recipient_id", $remote_user->id)->first();
                    if ($ref->activated == 0) {
                        $ref->activated = 1;
                        $ref->save();

                        $sender_user = User::where("id", $ref->user_sender_id)->first();
                        $sender_user->referral_bonus_count += env("REFERRAL_BONUS");
                        $sender_user->save();

                        $this->bot->sendRequest("sendMessage",
                            ["chat_id" => $sender_user->telegram_chat_id, "text" => "Вам начислено " . env("REFERRAL_BONUS") . " бонусов."]);
                    }


                    $this->bot->reply('Приз по акции успешно активирован');
                }
                $this->bot->reply('Спасибо, что пользуетесь нашей системой!)');
                break;

            default:
                $sender_user = \App\User::where("email", $user_id . "@t.me")
                    ->first();

                if ($sender_user != null) {
                    RefferalsHistory::create([
                        'user_sender_id' => $sender_user->id,
                        'user_recipient_id' => $user->id,
                        'activated' => 0,
                    ]);

                }

                break;
        }


        $this->bot->sendRequest("sendMessage",
            ["text" => 'Добрый день! Приветствуем вас в нашем акционном боте! У нас вы сможете найти самые актуальные ак', 'reply_markup' => json_encode([
                'keyboard' => $this->keyboard,
                'one_time_keyboard' => true,
                'resize_keyboard' => true
            ])
            ]);
    }


}
