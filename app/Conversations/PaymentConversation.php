<?php

namespace App\Conversations;

use App\CashbackHistory;
use App\CashBackInfo;
use App\Classes\CustomBotMenu;
use App\Company;
use App\Enums\AchievementTriggers;
use App\Events\AchievementEvent;
use App\Events\ActivateUserEvent;
use App\Events\AddCashBackEvent;
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
            $this->mainMenu(__("messages.menu_title_1"));
        }

    }

    public function askForAction()
    {
        $question = Question::create(__("messages.ask_action"))
            ->addButtons([
                Button::create(__("messages.ask_action_btn_1"))->value('askforpay'),
                Button::create(__("messages.ask_action_btn_2"))->value('addcashback'),
            ]);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();

                if ($selectedValue == "askforpay") {
                    $this->askForPay();
                }

                if ($selectedValue == "addcashback") {
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

            $cbi = CashBackInfo::where("company_id", $this->company_id)
                ->where("user_id", $recipient_user->id)
                ->first();

            if (env("INDIVIDUAL_CASHBACK_MODE"))
                $money = $recipient_user->referral_bonus_count + (is_null($cbi) ? 0 : $cbi->value);
            else
                $money = $recipient_user->referral_bonus_count + $recipient_user->cashback_bonus_count;

            $canPay = $money >= intval($nedded_bonus);

            if ($canPay) {

                RefferalsPaymentHistory::create([
                    'user_id' => $recipient_user->id,
                    'company_id' => $this->company_id,
                    'employee_id' => $this->getUser()->id,
                    'value' => intval($nedded_bonus),
                ]);

                if ($recipient_user->referral_bonus_count <= intval($nedded_bonus)) {
                    $module = intval($nedded_bonus) - $recipient_user->referral_bonus_count;
                    $recipient_user->referral_bonus_count = 0;
                    if (!env('INDIVIDUAL_CASHBACK_MODE'))
                        $recipient_user->cashback_bonus_count -= $module;
                    else {
                        $cbi->value -= $module;
                        $cbi->save();
                    }

                } else
                    $recipient_user->referral_bonus_count -= intval($nedded_bonus);

                $recipient_user->save();

                event(new ActivateUserEvent($recipient_user));
                event(new AchievementEvent(
                        AchievementTriggers::MaxCashBackRemoveBonus,
                        $nedded_bonus,
                        $recipient_user
                    )
                );

                $company_name = Company::find($this->company_id)->title;
                Telegram::sendMessage([
                    'chat_id' => $recipient_user->telegram_chat_id,
                    'parse_mode' => 'Markdown',
                    'text' => " _ $recipient_user->updated_at _ в *$company_name* произведено списание $nedded_bonus бонусов",
                ]);

                $this->mainMenu("Спасибо! Успешно списалось $nedded_bonus руб.");
                return;
            }

            if (!$canPay) {
                $keyboard = [
                    [
                        ["text"=>"CashBack по компаниям","callback_data"=>'/get_cashback_by_companies']
                    ]
                ];
                Telegram::sendMessage([
                    'chat_id' => $recipient_user->telegram_chat_id,
                    'parse_mode' => 'Markdown',
                    'text' => "\xE2\x9D\x97Внимание\xE2\x9D\x97Требуется списать *$nedded_bonus* руб, но у вас на счету только $money руб. (".$cbi->company->title.")",
                    'disable_notification' => 'false',
                    'reply_markup' => json_encode([
                        'inline_keyboard' => $keyboard,
                    ])
                ]);

                $this->reply("У пользователя недостаточно боунсных баллов! В наличии $money руб.");

                $this->askForAction();
                return;
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
                return;
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

        event(new AddCashBackEvent(
            $user->id,
            $this->company_id,
            $this->money_in_check
        ));

        CashbackHistory::create([
            'money_in_check' => $this->money_in_check,
            'activated' => 1,
            'employee_id' => $this->getUser()->id,
            'company_id' => $this->company_id,
            'check_info' => $this->check_info,
            'user_phone' => $this->phone ?? null,
            'user_id' => $user->id,
        ]);

        $this->mainMenu("Отлично! CashBack начислен пользователю " . (
                $user->phone ??
                $user->fio_from_telegram ??
                $user->name ??
                $user->email
            )
        );


    }
}
