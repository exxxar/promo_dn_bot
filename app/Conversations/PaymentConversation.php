<?php

namespace App\Conversations;

use App\User;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class PaymentConversation extends Conversation
{
    use CustomConversation;


    protected $request_id;
    protected $company_id;
    protected $bot;

    public function __construct($bot, $request_id,$company_id)
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

        if ($this->user->is_admin==1)
            $this->askForPay();

    }

    public function askForPay()
    {
        $question = Question::create('Введите желаемую для списания сумму')
            ->fallback('Ничего страшного, в следующий раз получится!') ;

        $this->ask($question, function (Answer $answer) {
          $nedded_bonus = $answer->getText();

          $recipient_user = User::where("telegram_chat_id",$this->request_id)->first();
          if ($recipient_user->referral_bonus_count+$recipient_user->cashback_bonus_count>intval($nedded_bonus))
          {
              $keyboard = [
                  'inline_keyboard' => [
                      [
                          ['text' => 'Списать '.$nedded_bonus." бонусов", 'callback_data' => "/payment_accept $nedded_bonus ".$this->user->id." ".$this->company_id],
                          ['text' => 'Отклонить', 'callback_data' => "/payment_decline $nedded_bonus ".$this->user->id." ".$this->company_id]
                      ]
                  ]
              ];

              $this->bot->sendRequest("sendMessage",
                  [
                      "text" => 'У вас новый запрос на списание бонусов!',
                      "chat_id"=>$recipient_user->telegram_chat_id,
                      'reply_markup' => json_encode($keyboard)
                  ]);

          }

          $this->bot->reply("У пользователя недостаточно боунсных баллов!");

        });
    }
}
