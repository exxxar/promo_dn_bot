<?php

namespace App\Conversations;

use App\Classes\CustomBotMenu;
use App\Prize;
use App\Promocode;
use App\User;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Support\Facades\Log;

class LotteryConversation extends Conversation
{
    use CustomBotMenu;

    public function __construct($bot)
    {
        $this->setBot($bot);
    }

    public function askPromocode()
    {
        $question = Question::create(__("messages.ask_promocode"))
            ->fallback(__("messages.ask_fallback"));

        return $this->ask($question, function (Answer $answer) {
            $code = Promocode::where("code", $answer->getText())
                ->first();

            if ($code == null) {
                $this->reply(__("messages.ask_promocode_error_1"));
                $this->askPromocode();
                return;
            }
            if ($code->activated == true) {
                $this->reply(__("messages.ask_promocode_error_2"));
                $this->askPromocode();
                return;
            }
            $prizes = json_decode(Prize::where("is_active", 1)
                ->where("company_id", $code->company_id)
                ->get(), true);

            $prizes = array_filter($prizes, function ($item) {
                return $item["summary_activation_count"] > $item["current_activation_count"];

            });

            if (count($prizes) == 0) {
                $this->reply(__("messages.ask_promocode_error_3"));
                return;
            }
            $code->activated = true;
            $code->user_id = $this->getUser()->id;
            $code->save();


            shuffle($prizes);
            $inline_keyboard = [];
            $tmp_menu = [];
            foreach ($prizes as $key => $prize) {
                $index = $key + 1;
                array_push($tmp_menu, ["text" => "\xF0\x9F\x8E\xB4", "callback_data" => "/check_lottery_slot " . $prize["id"] . " " . $code->id]);
                if ($index % 5 == 0 || count($prizes) == $index) {
                    array_push($inline_keyboard, $tmp_menu);
                    $tmp_menu = [];
                }
            }

            $this->sendMessage(__("messages.ask_promocode_success_1"), $inline_keyboard);
            $this->mainMenu(__("messages.menu_title_5"));
        });
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        try {
            $this->conversationMenu(__("messages.menu_title_3"));
            $this->askPromocode();
        } catch (\Exception $e) {
            Log::error(get_class($this));
            $this->mainMenu(__("messages.menu_title_1"));
        }
    }
}
