<?php


namespace App\Classes;


trait tBotConversation
{
    use tBotStorage;

    protected $is_conversation_active;

    public function startConversation(){
        $this->is_conversation_active = true;
    }

    public function stopConversation(){
        $this->is_conversation_active = false;
    }

    public function onConversation(){
        return $this->is_conversation_active;
    }


}
