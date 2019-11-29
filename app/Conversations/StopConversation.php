<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;

class StopConversation extends Conversation
{

    use CustomConversation;

    protected $bot;

    public function __construct($bot)
    {
        $this->bot = $bot;
    }

    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->stopsConversation();
        $this->mainMenu("Хорошего дня!");
    }


}
