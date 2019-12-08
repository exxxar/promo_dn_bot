<?php

namespace App\Conversations;

use App\CashbackHistory;
use App\Company;
use App\Enums\AchievementTriggers;
use App\Events\AchievementEvent;
use App\RefferalsPaymentHistory;
use App\User;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Carbon\Carbon;
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

        $this->user->updated_at = Carbon::now();
        $this->user->save();

        if ($this->user->is_admin == 1) {
            $this->conversationMenu("Начнем-с...");
            $this->askForAction();
        }

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
                    $this->askForCashback();
                }
            }
        });
    }

    public function askForPay()
    {
        $question = Question::create('Введите желаемую для списания сумму')
            ->fallback('Ничего страшного, в следующий раз получится!');

        $this->ask($question, function (Answer $answer) {
            $nedded_bonus = $answer->getText();

            $recipient_user = User::where("telegram_chat_id", intval($this->request_id))->first();
            if (!$recipient_user) {
                $this->mainMenu("Что-то пошло не так и пользователь не найден");
                return;
            }


            if ($recipient_user->referral_bonus_count + $recipient_user->cashback_bonus_count > intval($nedded_bonus)) {

                RefferalsPaymentHistory::create([
                    'user_id' => $recipient_user->id,
                    'company_id' => $this->company_id,
                    'employee_id' => $this->user->id,
                    'value' => intval($nedded_bonus),
                ]);

                if ($recipient_user->referral_bonus_count <= intval($nedded_bonus)) {
                    $module = intval($nedded_bonus) - $recipient_user->referral_bonus_count;
                    $recipient_user->referral_bonus_count = 0;
                    $recipient_user->cashback_bonus_count -= $module;
                } else
                    $recipient_user->referral_bonus_count -= intval($nedded_bonus);

                $recipient_user->activated = 1;
                $recipient_user->updated_at = Carbon::now();
                $recipient_user->save();

                event(new AchievementEvent(AchievementTriggers::MaxCashBackRemoveBonus, $nedded_bonus, $recipient_user));

                $company_name = Company::find($this->company_id)->title;
                Telegram::sendMessage([
                    'chat_id' => $recipient_user->telegram_chat_id,
                    'parse_mode' => 'HTML',
                    'text' => " _ $recipient_user->updated_at _ *$company_name* в произвели списание $nedded_bonus бонусов",
                ]);


            } else
                $this->mainMenu("У пользователя недостаточно боунсных баллов!");

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


        $user = User::where("telegram_chat_id", intval($this->request_id))->first();

        if (!$user) {
            $this->mainMenu("Что-то пошло не так и пользователь не найден");
            return;
        }


        $cashBack = round(intval($this->money_in_check) * env("CAHSBAK_PROCENT") / 100);
        $user->cashback_bonus_count += $cashBack;
        $user->activated = 1;
        $user->save();

        event(new AchievementEvent(AchievementTriggers::MaxCashBackCount, $cashBack, $user));

        CashbackHistory::create([
            'money_in_check' => $this->money_in_check,
            'activated' => 1,
            'employee_id' => $this->user->id,
            'company_id' => $this->company_id,
            'check_info' => $this->check_info,
            'user_phone' => $this->phone ?? null,
            'user_id' => $user->id,
        ]);

        Telegram::sendMessage([
            'chat_id' => $user->telegram_chat_id,
            'parse_mode' => 'Markdown',
            'text' => "Сумма в чеке *$this->money_in_check* руб.\nВам начислен *CashBack* в размере *$cashBack* руб.",
            'disable_notification' => 'false'
        ]);
        $this->mainMenu("Отлично! CashBack начислен пользователю " . ($user->phone ?? $user->fio_from_telegram ?? $user->name ?? $user->email));


    }
}
