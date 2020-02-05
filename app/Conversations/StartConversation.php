<?php

namespace App\Conversations;

use App\Category;
use App\Classes\CustomBotMenu;
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
    use CustomBotMenu;

    protected $bot;

    public function __construct($bot)
    {

        $this->setBot($bot);
    }

    public function run()
    {
        try {
            $this->startWithEmptyData();
        } catch (\Exception $e) {
            Log::error(get_class($this) . " " . $e->getMessage() . " " . $e->getLine());
            $this->fallbackMenu(__("messages.menu_title_7"));
        }
    }

    public function startWithEmptyData()
    {

        $user = $this->getUser();

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

        $this->mainMenu(__("messages.menu_title_8"));

        $categories = Category::orderBy('position', 'DESC')
            ->take(config("bot.results_per_page"))
            ->get();

        if (count($categories) == 0) {
            $this->reply(__("messages.message_4"));
            return;
        }

        foreach ($categories as $cat) {

            $keyboard = [
                [
                    ["text" => __("messages.start_con_btn_1"), "callback_data" => "/category " . $cat->id . " 0"]
                ]
            ];

            $this->sendPhoto("*$cat->title*", $cat->image_url, $keyboard);
        }

        $this->pagination("/promo_by_category", $categories, 0, __("messages.ask_action"));


    }
}
