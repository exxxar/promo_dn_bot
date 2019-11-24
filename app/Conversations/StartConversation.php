<?php

namespace App\Conversations;

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

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

        $username = $telegramUser->getUsername();
        $lastName = $telegramUser->getLastName();
        $firstName = $telegramUser->getFirstName();

        $user = \App\User::where("email", "$id@t.me")
            ->first();

        if ($user==null) {

            $user = \App\User::create([
                'name' => $username,
                'email' => "$id@t.me",
                'password' => bcrypt($id),
                'fio_from_telegram' => "$firstName $lastName",
                'fio_from_request' => '',
                'phone' => '',
                'avatar_url' => '$telegramUser->getUserProfilePhotos()[0]->file_path',
                'address' => '',
                'sex' => 0,
                'age' => 18,
                'source' => "000",
                'telegram_chat_id' => $id,
                'referrals_count' => 0,
                'referral_bonus_count' => 0,
                'cashback_bonus_count' => 0,
                'is_admin' => false,
            ]);

        }

        $this->bot->sendRequest("sendMessage",
            ["text" => "Добрый день! $id $username $firstName $lastName!Приветствуем вас в нашем акционном боте! У нас вы сможете найти самые актуальные ак", 'reply_markup' => json_encode([
                'keyboard' => $this->keyboard,
                'one_time_keyboard' => true,
                'resize_keyboard' => true
            ])
            ]);

        $categories = \App\Category::all();

        $tmp = [];

        foreach ($categories as $cat) {
            array_push($tmp, Button::create($cat->title)->value("/category " . $cat->id));
        }

        $message = Question::create("Категории акций:")
            ->addButtons($tmp);


        $this->bot->reply($message);
    }
}
