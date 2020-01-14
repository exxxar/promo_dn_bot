<?php

namespace App\Conversations;

use App\Prize;
use App\Promocode;
use App\User;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;

class LotteryConversation extends Conversation
{
    use CustomConversation;

    protected $bot;
    protected $chat_id;

    public function __construct($bot)
    {
        $telegramUser = $bot->getUser()->getId();

        $this->bot = $bot;
        $this->chat_id = $telegramUser;
    }
    /**
     * First question
     */
    public function askReason()
    {
        $question = Question::create("Начнем розыгрыш с ввода промокода:")
            ->fallback('Упс... что-то пошло не так!')
            ->callbackId('ask_reason');

        return $this->ask($question, function (Answer $answer) {
            $code = Promocode::where("code", $answer->getText())
                ->first();

            if ($code == null) {
                $this->bot->reply("Такой код не существует!");
                $this->askReason();
                return;
            }
            if ($code->activated == true) {
                $this->bot->reply("Код уже был использован");
                $this->askReason();
                return;
            }
            $prizes = json_decode(Prize::where("is_active",1)->where("company_id",$code->company_id)->get(), true);

            $prizes = array_filter ($prizes, function ($item){
                return $item["summary_activation_count"]>$item["current_activation_count"];

            });

            if (count($prizes) == 0) {
                $this->bot->reply("Увы, в лотерее еще не появились призы\xF0\x9F\x98\x94");
                return;
            }
            $code->activated = true;
            $code->user_id = (User::where("telegram_chat_id", $this->chat_id)->first())->id;
            $code->save();


            shuffle($prizes);
            $inline_keyboard = [];
            $tmp_menu = [];
            foreach ($prizes as $key => $prize) {
                $index = $key + 1;
                array_push($tmp_menu, ["text" => "\xF0\x9F\x8E\xB4", "callback_data" => "/check_lottery_slot " . $prize["id"]." ".$code->id]);
                if ($index % 5 == 0 || count($prizes) == $index) {
                    array_push($inline_keyboard, $tmp_menu);
                    $tmp_menu = [];
                }
            }
            $this->bot->sendRequest("sendMessage",
                [
                    "chat_id" => $this->chat_id,
                    "text" => "*Код успешно активирован!*\nВыберите приз",
                    "parse_mode" => "Markdown",
                    'reply_markup' => json_encode([
                        'inline_keyboard' =>
                            $inline_keyboard
                    ])
                ]);

            $this->mainMenu("Оставайтесь с нами и получайте больше призов!");
        });
    }
    /**
     * Start the conversation
     */
    public function run()
    {
        try {
            $this->conversationMenu("Получайте удовольствие от использование системы!");
            $this->askReason();
        }catch (\Exception $e){
            $this->bot->reply($e->getMessage()." ".$e->getLine());
            $this->mainMenu("Что-то пошло не так");
        }
    }
}
