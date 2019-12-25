<?php

namespace App\Conversations;

use App\Category;
use App\RefferalsHistory;
use App\User;
use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use Illuminate\Support\Facades\Log;

class StartConversation extends Conversation
{
    use CustomConversation;

    protected $bot;

    public function __construct($bot)
    {
        $this->bot = $bot;
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        try {
            $this->startWithEmptyData();
        } catch (\Exception $e) {
            $this->fallbackMenu("Добрый день!Приветствуем вас в нашем акционном боте! Сейчас у нас технические работы.");
        }
    }

    /**
     * First question
     */
    public function startWithEmptyData()
    {

        $telegramUser = $this->bot->getUser();

        $id = $telegramUser->getId();

        $user = User::where("telegram_chat_id", $id)
            ->first();

        if ($user == null) {
            $user = $this->createUser($telegramUser);


            $on_refferal = RefferalsHistory::where("user_recipient_id", $user->id)->first();

            if (!$on_refferal) {
                $skidobot = User::where("email", "skidobot@gmail.com")->first();

                if ($skidobot) {
                    $skidobot->referrals_count += 1;
                    $skidobot->save();

                    $user->parent_id = $skidobot->id;
                    $user->save();
                }
            }
        }

        $this->mainMenu("Добрый день!Приветствуем вас в нашем акционном боте! Мы рады, что Вы присоединились к нам. Все акции будут активны с 5 января.");

        $categories = Category::orderBy('id', 'DESC')
            ->orderBy('position', 'DESC')
            ->get();

        if (count($categories) > 0) {
            $tmp = [];

            foreach ($categories as $cat) {
                array_push($tmp, Button::create($cat->title)->value("/category " . $cat->id));
            }

            $message = Question::create("Категории акций:")
                ->addButtons($tmp);


            $this->bot->reply($message);
        } else
            $this->bot->reply("К сожалению, сейчас акций нет, но они появятся в ближайшее время!");


    }
}
