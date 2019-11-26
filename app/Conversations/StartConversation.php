<?php

namespace App\Conversations;

use App\Category;
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
        $this->startWithEmptyData();
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

        if ($user == null)
            $user = $this->createUser($telegramUser);


        $this->bot->sendRequest("sendMessage", [
            "text" => "Добрый день!Приветствуем вас в нашем акционном боте! У нас вы сможете найти самые актуальные акции",
            'reply_markup' => json_encode([
                'keyboard' => $this->keyboard,
                'one_time_keyboard' => true,
                'resize_keyboard' => true
            ])
        ]);

        $categories = Category::all();

        $tmp = [];

        foreach ($categories as $cat) {
            array_push($tmp, Button::create($cat->title)->value("/category " . $cat->id));
        }

        $message = Question::create("Категории акций:")
            ->addButtons($tmp);


        $this->bot->reply($message);
    }
}
