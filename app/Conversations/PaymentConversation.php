<?php

namespace App\Conversations;

use App\CashbackHistory;
use App\Classes\CustomBotMenu;
use App\Company;
use App\Enums\AchievementTriggers;
use App\Events\AchievementEvent;
use App\Events\ActivateUserEvent;
use App\Events\NetworkCashBackEvent;
use App\RefferalsPaymentHistory;
use App\User;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class PaymentConversation extends Conversation
{
    use CustomBotMenu;

    protected $request_id;
    protected $company_id;
    protected $check_info;
    protected $money_in_check;

    public function __construct($bot, $request_id, $company_id)
    {

        $this->setBot($bot);
        $this->request_id = $request_id;
        $this->company_id = $company_id;
    }

    public function run()
    {
        try {
            $this->check_info = "";
            $this->money_in_check = 0;

            if ($this->getUser()->is_admin == 1) {
                $this->conversationMenu(__("messages.menu_title_2"));
                $this->askForAction();
            }
        } catch (\Exception $e) {
            Log::error(get_class($this));
            $this->mainMenu(__("messages.menu_title_1"));
        }

    }

    public function askForAction()
    {
        $question = Question::create(__("messages.ask_action"))
            ->addButtons([
                Button::create(__("messages.ask_action_btn_1"))->value('askpay'),
                Button::create(__("messages.ask_action_btn_2"))->value('getcashabck'),
            ]);

        $this->ask($question, function (Answer $answer) {
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
        $question = Question::create(__("messages.ask_for_pay"))
            ->fallback(__("messages.ask_fallback"));

        $this->ask($question, function (Answer $answer) {
            $nedded_bonus = $answer->getText();

            if (strlen(trim($nedded_bonus)) == 0 || !is_numeric($nedded_bonus)) {
                $this->askForPay();
                return;
            }

            $recipient_user = User::where("telegram_chat_id", intval($this->request_id))->first();
            if (!$recipient_user) {
                $this->mainMenu(__("messages.menu_title_6"));
                return;
            }


            if ($recipient_user->referral_bonus_count + $recipient_user->cashback_bonus_count >= intval($nedded_bonus)) {

                RefferalsPaymentHistory::create([
                    'user_id' => $recipient_user->id,
                    'company_id' => $this->company_id,
                    'employee_id' => $this->getUser()->id,
                    'value' => intval($nedded_bonus),
                ]);

                if ($recipient_user->referral_bonus_count <= intval($nedded_bonus)) {
                    $module = intval($nedded_bonus) - $recipient_user->referral_bonus_count;
                    $recipient_user->referral_bonus_count = 0;
                    $recipient_user->cashback_bonus_count -= $module;
                } else
                    $recipient_user->referral_bonus_count -= intval($nedded_bonus);
                $recipient_user->save();

                event(new ActivateUserEvent($recipient_user));

                event(new AchievementEvent(AchievementTriggers::MaxCashBackRemoveBonus, $nedded_bonus, $recipient_user));

                $company_name = Company::find($this->company_id)->title;
                Telegram::sendMessage([
                    'chat_id' => $recipient_user->telegram_chat_id,
                    'parse_mode' => 'Markdown',
                    'text' => " _ $recipient_user->updated_at _ в *$company_name* произведено списание $nedded_bonus бонусов",
                ]);

                $this->mainMenu("Спасибо! Успешно списалось $nedded_bonus руб.");
            } else {
                $money = $recipient_user->referral_bonus_count + $recipient_user->cashback_bonus_count;

                Telegram::sendMessage([
                    'chat_id' => $recipient_user->telegram_chat_id,
                    'parse_mode' => 'Markdown',
                    'text' => "\xE2\x9D\x97Внимание\xE2\x9D\x97Требуется списать *$nedded_bonus* руб, но у вас на счету только $money руб.",
                    'disable_notification' => 'false'
                ]);

                $this->reply("У пользователя недостаточно боунсных баллов! В наличии $money руб.");

                $this->askForAction();
            }

        });
    }

    public function askForCashback()
    {
        $question = Question::create(__("messages.ask_for_cashback"))
            ->fallback(__("messages.ask_fallback"));

        $this->ask($question, function (Answer $answer) {
            $this->money_in_check = $answer->getText();
            if (strlen(trim($this->money_in_check)) == 0 || !is_numeric($this->money_in_check)) {
                $this->askForCashback();
                return;
            }

            $this->askForCheckInfo();
        });
    }

    public function askForCheckInfo()
    {
        $question = Question::create(__("messages.ask_for_check_info"))
            ->fallback(__("messages.ask_fallback"));

        $this->ask($question, function (Answer $answer) {
            $this->check_info = $answer->getText();
            if (strlen(trim($this->check_info)) == 0) {
                $this->askForCheckInfo();
            }
            $this->saveCashBack();
        });
    }

    public function saveCashBack()
    {

        $user = User::where("telegram_chat_id", intval($this->request_id))->first();

        if ($user == null) {
            $this->mainMenu(__("messages.menu_title_6"));
            return;
        }

        $cashBack = round(intval($this->money_in_check) * env("CAHSBAK_PROCENT") / 100);
        $user->cashback_bonus_count += $cashBack;
        $user->save();

        event(new ActivateUserEvent($user));
        event(new NetworkCashBackEvent($user->id, $cashBack));
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

        $companyName = Company::find($this->company_id)->title ?? "Неизвестная компания";

        Telegram::sendMessage([
            'chat_id' => $user->telegram_chat_id,
            'parse_mode' => 'Markdown',
            'text' => "Сумма в чеке *$this->money_in_check* руб.\nВам начислен *CashBack* в размере *$cashBack* руб от компании *$companyName*",
            'disable_notification' => 'false'
        ]);
        $this->mainMenu("Отлично! CashBack начислен пользователю " . ($user->phone ?? $user->fio_from_telegram ?? $user->name ?? $user->email));


    }
}
