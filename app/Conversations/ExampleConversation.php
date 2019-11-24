<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;

class ExampleConversation extends Conversation
{

    public function askFirstname()
    {

    }


    public function run()
    {
        $this->askFirstname();
    }
}
