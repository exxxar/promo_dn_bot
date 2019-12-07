<?php

namespace App\Listeners;

use App\Achievement;
use App\Events\AchievementEvent;
use App\Stat;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class AchievementProcessor
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
    public function handle(AchievementEvent $event)
    {
        //
        $stats = Stat::where("stat_type", $event->trigger_type)
            ->where("user_id", $event->user->id)
            ->first();

        if (!empty($stats)) {
            $stats->stat_value += $event->trigger_value;
            $stats->save();
        } else {
            $stats = Stat::create([
                'stat_type' => $event->trigger_type,
                'stat_value' => $event->trigger_value,
                'user_id' => $event->user->id
            ]);
        }

        $achList = Achievement::where("trigger_type", $event->trigger_type)
            ->where("trigger_value", "<=", $stats->stat_value)
            ->get();


        $user = User::find($event->user->id);

        $userAchList = $user
            ->achievements()
            ->where("trigger_type", $event->trigger_type)
            ->get();


        Log::info("user ach list count " . count($userAchList));

        foreach ($achList as $ach) {
            $find = $user
                ->achievements()
                ->where("achievement_id", $ach->id)
                ->first();

            if ($find == null) {
                if (!$ach->is_active)
                    continue;

                $user->achievements()->attach($ach->id);
                //todo: отправляем в телеграм пользователю оповещение о том, что получена ачивка

                $announce_title = $ach->title;
                $announce_message = $ach->description;
                $announce_url = $ach->ach_image_url;

                Telegram::sendMessage([
                    'chat_id' => $user->telegram_chat_id,
                    'parse_mode' => 'Markdown',
                    'text' => "*Вы получили достижение*:\n*$announce_title*\n_ $announce_message _\n $announce_url",
                    'disable_notification' => 'false'
                ]);


            }
        }
    }
}
