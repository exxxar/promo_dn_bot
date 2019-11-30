<?php

namespace App\Conversations;

use App\CashbackHistory;
use App\User;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Support\Facades\Auth;
use Telegram\Bot\Laravel\Facades\Telegram;

class PaymentConversation extends Conversation
{
    use CustomConversation;


    protected $request_id;
    protected $company_id;
    protected $bot;

    public function __construct($bot, $request_id, $company_id)
    {
        $this->bot = $bot;
        $this->request_id = $request_id;
        $this->company_id = $company_id;
    }

    public function run()
    {
        $telegramUser = $this->bot->getUser();
        $id = $telegramUser->getId();

        $this->user = \App\User::where("telegram_chat_id", $id)
            ->first();

        if ($this->user->is_admin == 1)
            $this->askForPay();

    }

    public function askForPay()
    {
        $question = Question::create('Введите желаемую для списания сумму')
            ->fallback('Ничего страшного, в следующий раз получится!');

        $this->ask($question, function (Answer $answer) {
            $nedded_bonus = $answer->getText();

            $recipient_user = User::where("telegram_chat_id", $this->request_id)->first();
            if ($recipient_user->referral_bonus_count + $recipient_user->cashback_bonus_count > intval($nedded_bonus)) {
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'Списать ' . $nedded_bonus . " бонусов", 'callback_data' => "/payment_accept $nedded_bonus " . $this->user->id . " " . $this->company_id],
                            ['text' => 'Отклонить', 'callback_data' => "/payment_decline $nedded_bonus " . $this->user->id . " " . $this->company_id]
                        ]
                    ]
                ];

                $this->bot->sendRequest("sendMessage", [
                    "text" => 'У вас новый запрос на списание бонусов!',
                    "chat_id" => $recipient_user->telegram_chat_id,
                    'reply_markup' => json_encode([
                        'keyboard' => json_encode($keyboard),
                        'one_time_keyboard' => true,
                        'resize_keyboard' => true
                    ])
                ]);

            }

            $this->bot->reply("У пользователя недостаточно боунсных баллов!");

        });
    }

    public function askForAction()
    {
        $question = Question::create('Выбери действие')
            ->addButtons([
                Button::create('Списать средства')->value('askpay'),
                Button::create('Начислить CashBack')->value('getcashabck'),
            ]);

        $this->ask($question, function (Answer $answer) {
            // Detect if button was clicked:
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();

                if ($selectedValue == "askpay") {
                    $this->askForPay();
                }

                if ($selectedValue == "getcashabck") {
                    $this->conversationMenu("Начнем-с...");
                    $this->askForCashback();
                }
            }
        });
    }

    public function askForCashback()
    {
        $question = Question::create('Введите сумму из чека')
            ->fallback('Спасибо что пообщался со мной:)!');

        $this->ask($question, function (Answer $answer) {
            $this->money_in_check = $answer->getText();
            $this->askForCheckInfo();
        });
    }

    public function askForCheckInfo()
    {
        $question = Question::create('Введите номер чека')
            ->fallback('Спасибо что пообщался со мной:)!');

        $this->ask($question, function (Answer $answer) {
            $this->check_info = $answer->getText();

            $this->saveCashBack();
        });
    }

    public function saveCashBack()
    {


        $user = User::where("id", $this->request_id)->first();


        if ($user) {
            $cashBack = round(intval($this->money_in_check) * env("CAHSBAK_PROCENT") / 100);
            $user->cashback_bonus_count += $cashBack;
            $user->save();

            CashbackHistory::create([
                'money_in_check' => $this->money_in_check,
                'activated' => 1,
                'employee_id' => Auth::user()->id,
                'company_id' => $this->company_id,
                'check_info' => $this->check_info,
                'user_phone' => $user->phone ?? null,

            ]);

            Telegram::sendMessage([
                'chat_id' => $user->telegram_chat_id,
                'parse_mode' => 'Markdown',
                'text' => "Сумма в чеке *$this->money_in_check* руб.\nВам начислен *CashBack* в размере *$cashBack* руб.",
                'disable_notification' => 'false'
            ]);
            $this->mainMenu("Отлично! Вы справились!");

        } else
            $this->mainMenu("Что-то пошло не так и пользователь не найден!");
    }
}
