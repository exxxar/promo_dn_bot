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


    protected $data;
    protected $bot;

    public function __construct($bot, $data)
    {
        $this->bot = $bot;
        $this->data = $data;
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

          $recipient_user = User::where("telegram_chat_id",$this->data)->first();
          if ($recipient_user->referral_bonus_count+$recipient_user->cashback_bonus_count>intval($nedded_bonus))
          {
              $keyboard = [
                  'inline_keyboard' => [
                      [
                          ['text' => 'Списать '.$nedded_bonus." бонусов", 'callback_data' => "/payment_accept $nedded_bonus ".$this->user->id],
                          ['text' => 'Отклонить', 'callback_data' => "/payment_decline $nedded_bonus ".$this->user->id]
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
