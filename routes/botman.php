<?php

use App\CashbackHistory;
use App\Drivers\TelegramInlineQueryDriver;
use App\Event;
use App\Http\Controllers\BotManController;

use BotMan\BotMan\Facades\BotMan;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;


$botman = resolve('botman');

$botman->hears('Попробовать снова', BotManController::class . '@startConversation');
$botman->hears('/start', BotManController::class . '@startConversation');

$botman->hears('Продолжить позже', BotManController::class . '@stopConversation')->stopsConversation();

$botman->hears('/start ([0-9a-zA-Z=]+)', BotManController::class . '@startDataConversation');

$botman->hears('/promotion ([0-9]+)', BotManController::class . '@promoConversation');
$botman->hears('/lotusprofile ([0-9]+)', BotManController::class . '@lotusprofileConversation');

$botman->hears('/fillinfo', BotManController::class . '@fillInfoConversation');
$botman->hears('/payment ([0-9]{1,10}) ([0-9]{1,10})', BotManController::class . '@paymentConversation');

$botman->hears("\xE2\x9B\x84Мероприятия", function ($bot) {
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => 'Мероприятия', 'callback_data' => "/events 0"],
                ['text' => 'Призы!', 'url' => env("APP_PROMO_LINK")],
            ]
        ]
    ];

    $bot->sendRequest("sendMessage",
        ["text" => 'Мы готовим для вас самые крутые мероприятия в городе! Посмотри!', 'reply_markup' => json_encode($keyboard)
        ]);
});
$botman->hears("\xF0\x9F\x93\xB2Мои друзья", function ($bot) {
    $telegramUser = $bot->getUser();

    $id = $telegramUser->getId();

    $user = \App\User::where("telegram_chat_id", $id)->first();

    $ref = $user->referrals_count;

    $tmp_message = "Вы пригласили *$ref* друзей!\n_Делитесь Вашим QR-кодом и накапливайте баллы!_\n";

    if ($ref > 0) {
        $message = Question::create($tmp_message)
            ->addButtons([
                Button::create("Посмотреть друзей")->value("/friends 0")
            ]);

        $bot->reply($message, ["parse_mode" => "Markdown"]);
    } else
        $bot->reply($tmp_message, ["parse_mode" => "Markdown"]);


    $tmp_id = "$id";
    while (strlen($tmp_id) < 10)
        $tmp_id = "0" . $tmp_id;

    $code = base64_encode("001" . $tmp_id . "0000000000");

    $attachment = new Image("https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://t.me/" . env("APP_BOT_NAME") . "?start=$code");

    // Build message object
    $message = OutgoingMessage::create('_Ваш реферальный код_')
        ->withAttachment($attachment);


    // Reply message object
    $bot->reply($message, ["parse_mode" => "Markdown"]);


});
$botman->hears("\xF0\x9F\x92\xB3Мои баллы", function ($bot) {
    $telegramUser = $bot->getUser();

    $id = $telegramUser->getId();

    $user = \App\User::where("telegram_chat_id", $id)->first();

    if ($user != null) {
        $summary = $user->referral_bonus_count + $user->cashback_bonus_count;
        $cashback = $user->cashback_bonus_count;


        $tmp_message = "У вас *$summary* баллов, из них *$cashback* - бонус CashBack!\n_Для оплаты дайте отсканировать данный QR-код сотруднику!_\n";


        $tmp_buttons = [];

        array_push($tmp_buttons, Button::create("Моя статистика")->value("/statistic"));


        if ($user->phone != null) {

            $cashback_history = CashbackHistory::where("user_phone", $user->phone)
                ->get();

            if (count($cashback_history) > 0) {
                $tmp_money = 0;
                foreach ($cashback_history as $ch)
                    if ($ch->activated == 0)
                        $tmp_money += round(intval($ch->money_in_check) * env("CAHSBAK_PROCENT") / 100);


                if ($tmp_money > 0)
                    array_push($tmp_buttons, Button::create("Зачислить мне $tmp_money руб. CashBack")->value("/cashback_get"));


            }


        }

        $message = Question::create($tmp_message)
            ->addButtons($tmp_buttons);

        $bot->reply($message, ["parse_mode" => "Markdown"]);

        $tmp_id = "$id";
        while (strlen($tmp_id) < 10)
            $tmp_id = "0" . $tmp_id;

        $code = base64_encode("002" . $tmp_id . "0000000000");


        $attachment = new Image("https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://t.me/" . env("APP_BOT_NAME") . "?start=$code");

        // Build message object
        $message = OutgoingMessage::create('_Ваш код для оплаты_')
            ->withAttachment($attachment);

        // Reply message object
        $bot->reply($message, ["parse_mode" => "Markdown"]);

    }

});
$botman->hears("\xF0\x9F\x94\xA5По категориям", function ($bot) {
    $categories = \App\Category::all();

    $tmp = [];

    foreach ($categories as $cat) {
        array_push($tmp, Button::create($cat->title)->value("/category " . $cat->id));
    }

    $message = Question::create("Акции по категориям:")
        ->addButtons($tmp);


    $bot->reply($message);
});
$botman->hears("\xF0\x9F\x94\xA5По компаниям", function ($bot) {
    $companies = \App\Company::all();

    $tmp = [];

    foreach ($companies as $company) {
        array_push($tmp, Button::create($company->title)->value("/company " . $company->id));
    }

    $message = Question::create("Акции по компаниям:")
        ->addButtons($tmp);


    $bot->reply($message);
});
$botman->hears("\xE2\x9A\xA1Все акции", function ($bot) {
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => 'Все акции на нашем сайте!', 'url' => env("APP_PROMO_LINK")],
            ],
            [
                ['text' => 'Празднуй новый год с нами!', 'url' => env("APP_PROMO_LINK")],
            ]
        ]
    ];

    $bot->sendRequest("sendMessage",
        ["text" => 'Посмотрите список всех акций на нашем сайте!', 'reply_markup' => json_encode($keyboard)
        ]);


});
$botman->hears('stop', function ($bot) {
    $bot->reply('Хорошо, продолжим позже!)');
})->stopsConversation();

$botman->hears('/category ([0-9]+)', function ($bot, $category_id) {

    $promotions = \App\Promotion::with(["users"])->where("category_id", "=", $category_id)
        ->get();

    $tmp = [];

    foreach ($promotions as $promo) {

        $telegramUser = $bot->getUser();
        $id = $telegramUser->getId();

        $on_promo = $promo->users()->where('telegram_chat_id', "$id")->first();

        $time_0 = (date_timestamp_get(new DateTime($promo->start_at)));
        $time_1 = (date_timestamp_get(new DateTime($promo->end_at)));

        $time_2 = date_timestamp_get(now());

        if ($on_promo == null && $time_2 >= $time_0 && $time_2 < $time_1) {
            if ($promo->handler == null)
                array_push($tmp, Button::create($promo->title)->value("/promotion " . $promo->id));
            else
                array_push($tmp, Button::create($promo->title)->value($promo->handler . " " . $promo->id));
        }
    }


    if (count($tmp) > 0) {
        $message = Question::create("Акции в категории:")
            ->addButtons($tmp);


        $bot->reply($message);
    } else
        $bot->reply("Акций в категории не найдено или все акции собраны:(");
});
$botman->hears('/company ([0-9]+)', function ($bot, $company_id) {

    $company = \App\Company::find($company_id);

    $attachment = new Image($company->logo_url);

    // Build message object
    $message = OutgoingMessage::create($company->title . "\n_" . $company->description . "_")
        ->withAttachment($attachment);

    // Reply message object
    $bot->reply($message, ["parse_mode" => "Markdown"]);

    $promotions = \App\Promotion::with(["users"])->where("company_id", "=", $company_id)
        ->get();

    $tmp = [];

    foreach ($promotions as $promo) {

        $telegramUser = $bot->getUser();
        $id = $telegramUser->getId();

        $on_promo = $promo->users()->where('telegram_chat_id', "$id")->first();

        $time_0 = (date_timestamp_get(new DateTime($promo->start_at)));
        $time_1 = (date_timestamp_get(new DateTime($promo->end_at)));

        $time_2 = date_timestamp_get(now());

        if ($on_promo == null && $time_2 >= $time_0 && $time_2 < $time_1) {
            if ($promo->handler == null)
                array_push($tmp, Button::create($promo->title)->value("/promotion " . $promo->id));
            else
                array_push($tmp, Button::create($promo->title)->value($promo->handler . " " . $promo->id));
        }
    }


    if (count($tmp) > 0) {
        $message = Question::create("Акции от компании:")
            ->addButtons($tmp);


        $bot->reply($message);
    } else
        $bot->reply("Акций от компании не найдено или все акции данной компании собраны:(");
});

$botman->hears('/friends ([0-9]+)', function ($bot, $page) {

    $telegramUser = $bot->getUser();

    $id = $telegramUser->getId();

    $user = \App\User::where("telegram_chat_id", $id)->first();

    $refs = \App\RefferalsHistory::with(["recipient"])
        ->where("user_sender_id", $user->id)
        ->skip($page * 10)
        ->take(10)
        ->orderBy('id', 'DESC')
        ->get();

    $tmp = "";

    foreach ($refs as $key => $ref) {
        $userName = $ref->recipient->fio_from_telegram ??
            $ref->recipient->fio_from_request ??
            $ref->recipient->telegram_chat_id;
        $tmp .= ($key + 1) . ". " . $userName . ($ref->activated == 0 ? "\xE2\x9D\x8E" : "\xE2\x9C\x85") . "\n";

    }

    $inline_keyboard = [];
    if ($page == 0 && count($refs) == 10)
        array_push($inline_keyboard, ['text' => 'Далее', 'callback_data' => '/friends ' . ($page + 1)]);

    if ($page > 0) {
        if (count($refs) == 0) {
            array_push($inline_keyboard, ['text' => 'Назад', 'callback_data' => '/friends ' . ($page - 1)]);
        }

        if (count($refs) == 10) {
            array_push($inline_keyboard, ['text' => 'Назад', 'callback_data' => '/friends ' . ($page - 1)]);
            array_push($inline_keyboard, ['text' => 'Далее', 'callback_data' => '/friends ' . ($page + 1)]);
        }

        if (count($refs) > 0 && count($refs) < 10) {
            array_push($inline_keyboard, ['text' => 'Назад', 'callback_data' => '/friends ' . ($page - 1)]);
        }
    }


    $keyboard = [
        'inline_keyboard' => [
            $inline_keyboard
        ]
    ];

    $bot->sendRequest("sendMessage",
        [
            "text" => strlen($tmp) > 0 ? $tmp : "У вас нет друзей:(",
            'reply_markup' => json_encode($keyboard)
        ]);


});
$botman->hears('/payments ([0-9]+)', function ($bot, $page) {

    $telegramUser = $bot->getUser();

    $id = $telegramUser->getId();

    $user = \App\User::where("telegram_chat_id", $id)->first();

    if ($user->phone == null) {
        $message = Question::create("У вас не заполнена личная информация и вы не можете просматривать историю оплаты.")
            ->addButtons([
                Button::create("Заполнить")->value("/fillinfo"),
            ]);
        $bot->reply($message, ["parse_mode" => "Markdown"]);
        return;
    }

    $refs = \App\RefferalsPaymentHistory::with(["company"])
        ->where("user_id", $user->id)
        ->skip($page * 10)
        ->take(10)
        ->orderBy('id', 'DESC')
        ->get();

    $tmp = "";

    foreach ($refs as $key => $ref) {
        $company_title = $ref->company->title ?? $ref->company->id;
        $tmp .= "_" . $ref->created_at . "_ в " . $company_title . " потрачено *" . $ref->value . "* боунсов \n";

    }

    $inline_keyboard = [];
    if ($page == 0 && count($refs) == 10)
        array_push($inline_keyboard, ['text' => 'Далее', 'callback_data' => '/payments ' . ($page + 1)]);

    if ($page > 0) {
        if (count($refs) == 0) {
            array_push($inline_keyboard, ['text' => 'Назад', 'callback_data' => '/payments ' . ($page - 1)]);
        }

        if (count($refs) == 10) {
            array_push($inline_keyboard, ['text' => 'Назад', 'callback_data' => '/payments ' . ($page - 1)]);
            array_push($inline_keyboard, ['text' => 'Далее', 'callback_data' => '/payments ' . ($page + 1)]);
        }

        if (count($refs) > 0 && count($refs) < 10) {
            array_push($inline_keyboard, ['text' => 'Назад', 'callback_data' => '/payments ' . ($page - 1)]);
        }
    }


    $keyboard = [
        'inline_keyboard' => [
            $inline_keyboard
        ]
    ];

    $bot->sendRequest("sendMessage",
        [
            "text" => strlen($tmp) > 0 ? $tmp : "Вы не потратили свои бонусы.",
            'reply_markup' => json_encode($keyboard),
            "parse_mode" => "Markdown"
        ]);


});
$botman->hears('/cashbacks ([0-9]+)', function ($bot, $page) {

    $telegramUser = $bot->getUser();

    $id = $telegramUser->getId();

    $user = \App\User::where("telegram_chat_id", $id)->first();

    if ($user->phone == null) {
        $message = Question::create("У вас не заполнена личная информация и вы не можете просматривать историю начисления CashBack.")
            ->addButtons([
                Button::create("Заполнить")->value("/fillinfo"),
            ]);
        $bot->reply($message, ["parse_mode" => "Markdown"]);
        return;
    }
    $cashbacks = CashbackHistory::where("user_id", $user->id)
        ->skip($page * 10)
        ->take(10)
        ->orderBy('id', 'DESC')
        ->get();

    $tmp = "";

    foreach ($cashbacks as $key => $cash) {
        $check_info = $cash->check_info;
        $cb = round(intval($cash->money_in_check) * env("CAHSBAK_PROCENT") / 100);
        $tmp .= "Завдение *" . $cash->company->title . "* _" . $cash->created_at . "_ чек №" . $check_info . " принес вам *" . $cb . "* руб. CashBack \n";

    }

    $inline_keyboard = [];
    if ($page == 0 && count($cashbacks) == 10)
        array_push($inline_keyboard, ['text' => 'Далее', 'callback_data' => '/cashbacks ' . ($page + 1)]);

    if ($page > 0) {
        if (count($cashbacks) == 0) {
            array_push($inline_keyboard, ['text' => 'Назад', 'callback_data' => '/cashbacks ' . ($page - 1)]);
        }

        if (count($cashbacks) == 10) {
            array_push($inline_keyboard, ['text' => 'Назад', 'callback_data' => '/cashbacks ' . ($page - 1)]);
            array_push($inline_keyboard, ['text' => 'Далее', 'callback_data' => '/cashbacks ' . ($page + 1)]);
        }

        if (count($cashbacks) > 0 && count($cashbacks) < 10) {
            array_push($inline_keyboard, ['text' => 'Назад', 'callback_data' => '/cashbacks ' . ($page - 1)]);
        }
    }


    $keyboard = [
        'inline_keyboard' => [
            $inline_keyboard
        ]
    ];

    $bot->sendRequest("sendMessage",
        [
            "text" => strlen($tmp) > 0 ? $tmp : "Вам не начислялся CashBack.",
            'reply_markup' => json_encode($keyboard),
            "parse_mode" => "Markdown"
        ]);


});
$botman->hears('/events ([0-9]+)', function ($bot, $page) {


    $events = Event::skip($page * 5)
        ->take(5)
        ->orderBy('id', 'DESC')
        ->get();

    if (count($events) > 0) {
        foreach ($events as $key => $event) {

            $attachment = new Image($event->event_image_url);
            $message = OutgoingMessage::create("*" . $event->title . "*\n" . $event->description)
                ->withAttachment($attachment);

            $bot->reply($message, ["parse_mode" => "Markdown"]);
        }
    } else
        $bot->reply("Мероприятия появтяся в скором времени!", ["parse_mode" => "Markdown"]);

    $inline_keyboard = [];
    if ($page == 0 && count($events) == 5)
        array_push($inline_keyboard, ['text' => 'Далее', 'callback_data' => '/events ' . ($page + 1)]);

    if ($page > 0) {
        if (count($events) == 0) {
            array_push($inline_keyboard, ['text' => 'Назад', 'callback_data' => '/events ' . ($page - 1)]);
        }

        if (count($events) == 5) {
            array_push($inline_keyboard, ['text' => 'Назад', 'callback_data' => '/events ' . ($page - 1)]);
            array_push($inline_keyboard, ['text' => 'Далее', 'callback_data' => '/events ' . ($page + 1)]);
        }

        if (count($events) > 0 && count($events) < 5) {
            array_push($inline_keyboard, ['text' => 'Назад', 'callback_data' => '/events ' . ($page - 1)]);
        }
    }


    $keyboard = [
        'inline_keyboard' => [
            $inline_keyboard
        ]
    ];

    if (count($inline_keyboard) > 0)
        $bot->sendRequest("sendMessage",
            [
                "text" => "Выберите действие",
                'reply_markup' => json_encode($keyboard)
            ]);


});
$botman->hears('/cashback_get', function ($bot) {
    $telegramUser = $bot->getUser();

    $id = $telegramUser->getId();

    $user = \App\User::where("telegram_chat_id", $id)->first();

    if ($user->phone != null) {

        $cashback_history = CashbackHistory::where("user_phone", $user->phone)
            ->get();

        if (count($cashback_history) > 0) {
            foreach ($cashback_history as $ch) {
                if ($ch->activated == 1)
                    continue;

                $ch->activated = 1;
                $ch->save();

                $user->cashback_bonus_count += round(intval($ch->money_in_check) * env("CAHSBAK_PROCENT") / 100);
                $user->save();

            }
            $bot->reply("CashBack успешно зачислен!", ["parse_mode" => "Markdown"]);
        }
    }


});
$botman->hears('/statistic', function ($bot) {
    $message = Question::create("Вы можете отслеживать начисления CashBack бонусов и их списание")
        ->addButtons([
            Button::create("Начисления")->value("/cashbacks 0"),
            Button::create("Списания")->value("/payments 0"),
        ]);
    $bot->reply($message, ["parse_mode" => "Markdown"]);

});


/*$botman->hears('start ([0-9]+)', function (\BotMan\BotMan\BotMan $bot, $movie) {

    Log::info("MI TYT");
    $bot->loadDriver(TelegramInlineQueryDriver::DRIVER_NAME);
    Log::info("TestttttRRR " . print_r(json_decode($bot->getDriver()->getEvent())->id, true));
    // Log::info("TestttttRRR " . print_r(json_decode($bot->getEvent()), true));
    /*
        $bot->sendRequest("answerInlineQuery",
            [
                "inline_query_id" => json_decode($bot->getEvent())->id,
                "results" => json_encode([
                        [
                            'type' => 'article',
                            'id' => uniqid(),
                            'title' => 'My result title 1',
                            'input_message_content' => [
                                'message_text' => 'My message text 111'
                            ]
                        ],
                        [
                            'type' => 'article',
                            'id' => uniqid(),
                            'title' => 'My result title 2',
                            'input_message_content' => [
                                'message_text' => 'My message text 222'
                            ]
                        ],
                        [
                            'type' => 'article',
                            'id' => uniqid(),
                            'title' => 'My result title 3',
                            'input_message_content' => [
                                'message_text' => 'My message text 333'
                            ]
                        ]
                    ]
                )
                //'reply_markup' => json_encode($keyboard)
            ]);

});*/

$botman->fallback(function ($bot) {
    Log::info("fallback");

    $bot->loadDriver(TelegramInlineQueryDriver::DRIVER_NAME);


    $queryObject = json_decode($bot->getDriver()->getEvent());

    if ($queryObject) {
        Log::info("Query " . $queryObject->query);
        Log::info("id " . $queryObject->id);

        return $bot->sendRequest("answerInlineQuery",
            [
                'cache_time' => 0,
                "inline_query_id" => json_decode($bot->getEvent())->id,
                "results" => json_encode([
                        [
                            'type' => 'article',
                            'id' => uniqid(),
                            'title' => 'Скидки! Получи 20% на пивное меню.',
                            'input_message_content' => [
                                'message_text' => "My message text 111 https://sun9-26.userapi.com/c857220/v857220055/58b91/h_QTsrOYTGg.jpg",
                            ],
                            'reply_markup'=>[
                                'inline_keyboard'=>[
                                    [
                                        ['text'=>"Поделиться ссылкой","url"=>"https://t.me/skidki_dn_bot"],
                                    ],
                                    [
                                        ['text'=>"Пригласить пользвоателя","switch_inline_query"=>"invite"],
                                    ]
                                ]
                            ],
                            'thumb_url'=>"https://sun9-26.userapi.com/c857220/v857220055/58b91/h_QTsrOYTGg.jpg",
                            'description'=>"Скидки на пиво!",

                        ],

                        [
                            'type' => 'article',
                            'id' => uniqid(),
                            'title' => 'XX',
                            'input_message_content' => [
                                'message_text' => 'My message text 333'
                            ]
                        ],
                        [
                            'type' => 'article',
                            'id' => uniqid(),
                            'title' => 'XX1',
                            'input_message_content' => [
                                'message_text' => 'My message text 333'
                            ]
                        ],
                        [
                            'type' => 'article',
                            'id' => uniqid(),
                            'title' => 'XX2',
                            'input_message_content' => [
                                'message_text' => 'My message text 333'
                            ]
                        ],
                        [
                            'type' => 'article',
                            'id' => uniqid(),
                            'title' => 'XX3',
                            'input_message_content' => [
                                'message_text' => 'My message text 333'
                            ]
                        ],
                        [
                            'type' => 'article',
                            'id' => uniqid(),
                            'title' => 'XX4',
                            'input_message_content' => [
                                'message_text' => 'My message text 333'
                            ]
                        ],
                        [
                            'type' => 'article',
                            'id' => uniqid(),
                            'title' => 'XX5',
                            'input_message_content' => [
                                'message_text' => 'My message text 333'
                            ]
                        ],
                        [
                            'type' => 'article',
                            'id' => uniqid(),
                            'title' => 'XX6',
                            'input_message_content' => [
                                'message_text' => 'My message text 333'
                            ]
                        ]
                    ]
                )
                //'reply_markup' => json_encode($keyboard)
            ]);
    }
});

