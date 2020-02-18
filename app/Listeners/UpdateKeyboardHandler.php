<?php

namespace App\Listeners;

use App\Events\UpdateKeyboardEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Telegram\Bot\Laravel\Facades\Telegram;

class UpdateKeyboardHandler
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(UpdateKeyboardEvent $event)
    {
        //

        Telegram::editMessageReplyMarkup([
            'chat_id' => $event->chat_id,
            "message_id" => $event->message_id,
            'reply_markup' => json_encode([
                'inline_keyboard' => $event->keyboard,
            ])
        ]);

    }
}
