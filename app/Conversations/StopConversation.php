<?php

namespace App\Conversations;

use App\Classes\CustomBotMenu;
use BotMan\BotMan\Messages\Conversations\Conversation;
use Illuminate\Support\Facades\Log;

class StopConversation extends Conversation
{

    use CustomBotMenu;

    public function __construct($bot)
    {
        $this->initKeyboards();
        $this->setBot($bot);
    }

    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->mainMenu(__("messages.menu_title_1"));
    }


}
