<?php

use App\Article;
use App\CashbackHistory;
use App\Conversations\StartConversation;
use App\Drivers\TelegramInlineQueryDriver;
use App\Event;
use App\Http\Controllers\BotManController;

use App\Prize;
use App\User;
use BotMan\BotMan\Facades\BotMan;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;


$botman = resolve('botman');

$botman->hears('Попробовать снова', BotManController::class . '@startConversation');
$botman->hears('/start', BotManController::class . '@startConversation');

$botman->hears('Продолжить позже', BotManController::class . '@stopConversation')->stopsConversation();

$botman->hears('/lottery', BotManController::class . '@lotteryConversation');

$botman->hears('/start ([0-9a-zA-Z=]+)', BotManController::class . '@startDataConversation');

$botman->hears('/promotion ([0-9]+)', BotManController::class . '@promoConversation');
$botman->hears('/lotusprofile ([0-9]+)', BotManController::class . '@lotusprofileConversation');

$botman->hears('/fillinfo', BotManController::class . '@fillInfoConversation');
$botman->hears('/payment ([0-9]{1,10}) ([0-9]{1,10})', BotManController::class . '@paymentConversation');

$botman->hears("\xE2\x9B\x84Мероприятия", function ($bot) {
    $events = Event::skip(0)
        ->take(5)
        ->orderBy('position', 'DESC')
        ->get();

    if (count($events) > 0) {
        $isActive = false;
        foreach ($events as $key => $event) {

            $time_0 = (date_timestamp_get(new DateTime($event->start_at)));
            $time_1 = (date_timestamp_get(new DateTime($event->end_at)));

            $time_2 = date_timestamp_get(now());

            if ($time_2 >= $time_0 && $time_2 < $time_1) {

                $isActive = true;

                $attachment = new Image($event->event_image_url);
                $message = OutgoingMessage::create("*" . $event->title . "*\n" . $event->description)
                    ->withAttachment($attachment);

                $bot->reply($message, ["parse_mode" => "Markdown"]);
            }
        }

        if (!$isActive)
            $bot->reply("Мероприятия появтяся в скором времени!", ["parse_mode" => "Markdown"]);
    } else
        $bot->reply("Мероприятия появтяся в скором времени!", ["parse_mode" => "Markdown"]);

    if (count($events) == 5)
        $bot->sendRequest("sendMessage",
            [
                "text" => "Выберите действие",
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            ['text' => "\xE2\x8F\xA9Далее", 'callback_data' => '/events 1']
                        ]
                    ]
                ])
            ]);
});

$botman->hears("/ref ([0-9]+)", function ($bot, $refId) {
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $user = User::where("telegram_chat_id", $id)->first();

    if ($user->phone == null) {
        $message = Question::create("У вас не заполнена личная информация! Заполняй и делись ссылкой:)")
            ->addButtons([
                Button::create("Заполнить")->value("/fillinfo"),
            ]);
        $bot->reply($message, ["parse_mode" => "Markdown"]);
        return;
    }

    $tmp_id = "$id";
    while (strlen($tmp_id) < 10)
        $tmp_id = "0" . $tmp_id;

    $comand_code = "001";
    switch ($refId) {
        default:
        case 1:
            $comand_code = "001";
            break;
        case 2:
            $comand_code = "004";
            break;
        case 3:
            $comand_code = "005";
            break;
        case 4:
            $comand_code = "006";
            break;
    }

    $code = base64_encode($comand_code . $tmp_id . "0000000000");


    $url_link = "https://t.me/" . env("APP_BOT_NAME") . "?start=$code";

    $href_url_link = "<a href='" . $url_link . "'>Пересылай сообщение друзьям и получай больше баллов!</a>";


    $bot->reply("Делись ссылкой с друзьями:\n" . ($refId == 1 ? $href_url_link : $url_link), ["parse_mode" => "HTML"]);

});

$botman->hears('.*Розыгрыш|/start_lottery_test', function ($bot) {
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $rules = Article::where("part",\App\Enums\Parts::Lottery)
        ->orderBy("id","DESC")
        ->first() ?? null;

    $keybord = [
        [
            ['text' => "Условия розыгрыша и призы", 'url' => $rules->url??'']
        ],
        [
            ['text' => "Ввести код и начать", 'callback_data' => "/lottery"]
        ]
    ];
    $bot->sendRequest("sendMessage",
        [
            "chat_id" => "$id",
            "text" => "Розыгрыш призов",
            "parse_mode" => "Markdown",
            'reply_markup' => json_encode([
                'inline_keyboard' =>
                    $keybord
            ])
        ]);
});
$botman->hears("\xF0\x9F\x93\xB2Мои друзья", function ($bot) {
    $telegramUser = $bot->getUser();

    $id = $telegramUser->getId();

    $user = \App\User::where("telegram_chat_id", $id)->first();

    if (!$user) {
        $bot->startConversation(new StartConversation($bot));
        return;
    }

    $ref = $user->referrals_count;

    $network = $user->network_friends_count + $ref;

    $network_tmp = $user->current_network_level > 0 ? "Сеть друзей *$network* чел.!\n" : "";

    $tmp_message = "Вы пригласили *$ref* друзей!\n" . $network_tmp . "_Делитесь Вашим QR-кодом и накапливайте баллы!_\n";

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

    $attachment = new Image(env("QR_URL")."https://t.me/" . env("APP_BOT_NAME") . "?start=$code");
   // $attachment = new Image(env("APP_URL") . "/image/?data=" . base64_encode("https://t.me/" . env("APP_BOT_NAME") . "?start=$code"));

    // Build message object
    $message = OutgoingMessage::create('_Ваш реферальный код_')
        ->withAttachment($attachment);


    // Reply message object
    $bot->reply($message, ["parse_mode" => "Markdown"]);


});
$botman->hears("\xE2\x9D\x93F.A.Q.", function ($bot) {


    $keyboard1 = [
        'inline_keyboard' => [
            [
                ['text' => "Как пользоваться", 'callback_data' => "/help"],
            ],
            [
                ['text' => "Реализация бонусов", 'callback_data' => "/rules"],
            ],
            [
                ['text' => "Промоутеру", 'callback_data' => "/promouter"],
                ['text' => "Поставщикам", 'callback_data' => "/suppliers"],
            ],
            [
                ['text' => "О нас", 'callback_data' => "/about"],
                ['text' => "Разработчики", 'callback_data' => "/dev"],
            ],
            [
                ['text'=>'Роззыгрыш','callback_data'=>'/start_lottery_test']
            ]


        ]
    ];



    $bot->sendRequest("sendMessage",
        [
            "text" => "*F.A.Q.*\n_Не знаете как начать пользоваться? - Почитайте наше описание! Узнайте больше о приложении, компании и разработчике!_",
            "parse_mode" => "Markdown",
            'reply_markup' => json_encode($keyboard1)
        ]);


});
$botman->hears("\xF0\x9F\x92\xB3Мои баллы", function ($bot) {
    $telegramUser = $bot->getUser();

    $id = $telegramUser->getId();

    $user = \App\User::where("telegram_chat_id", $id)->first();

    if (!$user) {
        $bot->startConversation(new StartConversation($bot));
        return;
    }


    $summary = $user->referral_bonus_count + $user->cashback_bonus_count + $user->network_cashback_bonus_count;
    $cashback = $user->cashback_bonus_count;

    $tmp_network = $user->network_friends_count >= 150 ? "Сетевой бонус *" . $user->network_cashback_bonus_count . "*\n" : '';

    $tmp_message = "У вас *$summary* баллов, из них *$cashback* - бонус CashBack!\n" . $tmp_network . "_Для оплаты дайте отсканировать данный QR-код сотруднику!_\n";


    $tmp_buttons = [];

    array_push($tmp_buttons, Button::create("Мой бюджет")->value("/statistic"));


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


     $attachment = new Image(env("QR_URL")."https://t.me/" . env("APP_BOT_NAME") . "?start=$code");
    //$attachment = new Image(env("APP_URL") . "/image/?data=" . base64_encode("https://t.me/" . env("APP_BOT_NAME") . "?start=$code"));

    // Build message object
    $message = OutgoingMessage::create('_Ваш код для оплаты_')
        ->withAttachment($attachment);

    // Reply message object
    $bot->reply($message, ["parse_mode" => "Markdown"]);


});
$botman->hears("/promo_by_category", function ($bot) {

    $telegramUser = $bot->getUser();

    $id = $telegramUser->getId();

    $user = \App\User::where("telegram_chat_id", $id)->first();

    if (!$user) {
        $bot->startConversation(new StartConversation($bot));
        return;
    }

    $categories = \App\Category::orderBy('position', 'DESC')
        ->get();

    $tmp = [];

    foreach ($categories as $cat) {
        array_push($tmp, Button::create($cat->title)->value("/category " . $cat->id));
    }

    $message = Question::create("Акции по категориям:")
        ->addButtons($tmp);


    $bot->reply($message);
});
$botman->hears("/promo_by_company", function ($bot) {

    $telegramUser = $bot->getUser();

    $id = $telegramUser->getId();

    $user = \App\User::where("telegram_chat_id", $id)->first();

    if (!$user) {
        $bot->startConversation(new StartConversation($bot));
        return;
    }

    $companies = \App\Company::orderBy('position', 'DESC')
        ->get();

    $tmp = [];

    foreach ($companies as $company) {
        array_push($tmp, Button::create($company->title)->value("/company " . $company->id));
    }

    $message = Question::create("Акции по компаниям:")
        ->addButtons($tmp);


    $bot->reply($message);
});
$botman->hears("\xF0\x9F\x94\xA5Акции", function ($bot) {
    
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => "\xF0\x9F\x92\x8EПо категориям", 'callback_data' => '/promo_by_category'],
                ['text' => "\xF0\x9F\x8F\xA6По компаниям", 'callback_data' => '/promo_by_company'],
            ],
            [
                ['text' => "\xE2\xAD\x90Достижения", 'callback_data' => "/achievements_panel"],
            ],
            [
                ['text' => "\xE2\x9A\xA1Акции и призы на сайте", 'url' => env("APP_PROMO_LINK")],
            ],

        ]
    ];

    $bot->sendRequest("sendMessage",
        ["text" => 'Самые свежие акции', 'reply_markup' => json_encode($keyboard)
        ]);


});
$botman->hears('stop', function ($bot) {
    $bot->reply('Хорошо, продолжим позже!)');
})->stopsConversation();
$botman->hears('/category ([0-9]+)', function ($bot, $category_id) {

    $promotions = \App\Promotion::with(["users"])->where("category_id", "=", $category_id)
        ->orderBy('position', 'DESC')
        ->get();

    $tmp = [];

    $isEmpty = true;
    foreach ($promotions as $promo) {

        $telegramUser = $bot->getUser();
        $id = $telegramUser->getId();

        $on_promo = $promo->users()->where('telegram_chat_id', "$id")->first();

        $time_0 = (date_timestamp_get(new DateTime($promo->start_at)));
        $time_1 = (date_timestamp_get(new DateTime($promo->end_at)));

        $time_2 = date_timestamp_get(now());

        if ($on_promo == null && $time_2 >= $time_0 && $time_2 < $time_1) {
            $isEmpty = false;

            $attachment = new Image($promo->promo_image_url);
            $message = OutgoingMessage::create()
                ->withAttachment($attachment);
            $bot->reply($message,["parse_mode"=>"Markdown"]);

            $message = Question::create("*".$promo->title."*")
                ->addButtons([
                    Button::create("\xF0\x9F\x91\x89Подробнее")->value($promo->handler==null?"/promotion " . $promo->id:$promo->handler . " " . $promo->id)
                ]);

            $bot->reply($message,["parse_mode"=>"Markdown"]);
        }
    }



    if ($isEmpty)
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
    $isEmpty = false;

    foreach ($promotions as $promo) {

        $telegramUser = $bot->getUser();
        $id = $telegramUser->getId();

        $on_promo = $promo->users()->where('telegram_chat_id', "$id")->first();

        $time_0 = (date_timestamp_get(new DateTime($promo->start_at)));
        $time_1 = (date_timestamp_get(new DateTime($promo->end_at)));

        $time_2 = date_timestamp_get(now());

        if ($on_promo == null && $time_2 >= $time_0 && $time_2 < $time_1) {


            $attachment = new Image($promo->promo_image_url);
            $message = OutgoingMessage::create()
                ->withAttachment($attachment);
            $bot->reply($message,["parse_mode"=>"Markdown"]);

            $message = Question::create("*".$promo->title."*")
                ->addButtons([
                    Button::create("\xF0\x9F\x91\x89Подробнее")->value($promo->handler==null?"/promotion " . $promo->id:$promo->handler . " " . $promo->id)
                ]);

            $bot->reply($message,["parse_mode"=>"Markdown"]);
        }
    }


    if ($isEmpty)
        $bot->reply("Акций в категории не найдено или все акции собраны:(");
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


    $sender = \App\RefferalsHistory::with(["sender"])
        ->where("user_recipient_id", $user->id)
        ->first();

    $tmp = "";

    if ($sender != null) {
        if ($sender->sender != null) {
            $userSenderName = $sender->sender->fio_from_telegram ??
                $sender->sender->fio_from_request ??
                $sender->sender->telegram_chat_id ??
                'Неизвестный пользователь';

            $tmp = "\xF0\x9F\x91\x91 $userSenderName - пригласил вас.\n";
        }
    }

    if ($refs != null)
        foreach ($refs as $key => $ref) {
            if ($ref->recipient != null) {
                $userName = $ref->recipient->fio_from_telegram ??
                    $ref->recipient->fio_from_request ??
                    $ref->recipient->telegram_chat_id ??
                    'Неизвестный пользователь';
                $tmp .= ($key + 1) . ". " . $userName . ($ref->activated == 0 ? "\xE2\x9D\x8E" : "\xE2\x9C\x85") . "\n";
            }
        }

    $inline_keyboard = [];
    if ($page == 0 && count($refs) == 10)
        array_push($inline_keyboard, ['text' => "\xE2\x8F\xA9Далее", 'callback_data' => '/friends ' . ($page + 1)]);

    if ($page > 0) {
        if (count($refs) == 0) {
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => '/friends ' . ($page - 1)]);
        }

        if (count($refs) == 10) {
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => '/friends ' . ($page - 1)]);
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xA9Далее", 'callback_data' => '/friends ' . ($page + 1)]);
        }

        if (count($refs) > 0 && count($refs) < 10) {
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => '/friends ' . ($page - 1)]);
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
        array_push($inline_keyboard, ['text' => "\xE2\x8F\xA9Далее", 'callback_data' => '/payments ' . ($page + 1)]);

    if ($page > 0) {
        if (count($refs) == 0) {
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => '/payments ' . ($page - 1)]);
        }

        if (count($refs) == 10) {
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => '/payments ' . ($page - 1)]);
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xA9Далее", 'callback_data' => '/payments ' . ($page + 1)]);
        }

        if (count($refs) > 0 && count($refs) < 10) {
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => '/payments ' . ($page - 1)]);
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
        $tmp .= "Заведение *" . $cash->company->title . "* _" . $cash->created_at . "_ чек №" . $check_info . " принес вам *" . $cb . "* руб. CashBack \n";

    }

    $inline_keyboard = [];
    if ($page == 0 && count($cashbacks) == 10)
        array_push($inline_keyboard, ['text' => "\xE2\x8F\xA9Далее", 'callback_data' => '/cashbacks ' . ($page + 1)]);

    if ($page > 0) {
        if (count($cashbacks) == 0) {
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => '/cashbacks ' . ($page - 1)]);
        }

        if (count($cashbacks) == 10) {
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => '/cashbacks ' . ($page - 1)]);
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xA9Далее", 'callback_data' => '/cashbacks ' . ($page + 1)]);
        }

        if (count($cashbacks) > 0 && count($cashbacks) < 10) {
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => '/cashbacks ' . ($page - 1)]);
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
        ->orderBy('position', 'DESC')
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
        array_push($inline_keyboard, ['text' => "\xE2\x8F\xA9Далее", 'callback_data' => '/events ' . ($page + 1)]);

    if ($page > 0) {
        if (count($events) == 0) {
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => '/events ' . ($page - 1)]);
        }

        if (count($events) == 5) {
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => '/events ' . ($page - 1)]);
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xA9Далее", 'callback_data' => '/events ' . ($page + 1)]);
        }

        if (count($events) > 0 && count($events) < 5) {
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => '/events ' . ($page - 1)]);
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
                $ch->user_id = $user->id;
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
$botman->hears('/achievements_panel', function ($bot) {
    $message = Question::create("Получайте достижения и обменивайте их на крутейшие призы!")
        ->addButtons([
            Button::create("\xF0\x9F\x8D\x80Все достижения")->value("/achievements_all 0"),
            Button::create("\xE2\xAD\x90Мои достижения")->value("/achievements_my 0"),
        ]);
    $bot->reply($message, ["parse_mode" => "Markdown"]);

});
$botman->hears('/achievements_all ([0-9]+)', function ($bot, $page) {

    $achievements = \App\Achievement::where("is_active", 1)
        ->skip($page * 5)
        ->take(5)
        ->orderBy('position', 'ASC')
        ->get();

    if (count($achievements) > 0) {

        foreach ($achievements as $key => $achievement) {

            $attachment = new Image($achievement->ach_image_url);
            $message = OutgoingMessage::create()
                ->withAttachment($attachment);

            $bot->reply($message, ["parse_mode" => "Markdown"]);

            $message = Question::create(($achievement->activated == 0 ? "" : "\xE2\x9C\x85") .
                ("*" .
                    $achievement->title . "*\n _" .
                    $achievement->description . "_\n")
            )
                ->addButtons([
                    Button::create("Подробнее")->value("/achievements_description " . $achievement->id)
                ]);
            $bot->reply($message, ["parse_mode" => "Markdown"]);
        }

    } else
        $bot->reply("Достижения будут доступны в скором времени!", ["parse_mode" => "Markdown"]);

    $inline_keyboard = [];
    if ($page == 0 && count($achievements) == 5)
        array_push($inline_keyboard, ['text' => "\xE2\x8F\xA9Далее", 'callback_data' => '/achievements_all ' . ($page + 1)]);

    if ($page > 0) {
        if (count($achievements) == 0) {
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => '/achievements_all ' . ($page - 1)]);
        }

        if (count($achievements) == 5) {
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => '/achievements_all ' . ($page - 1)]);
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xA9Далее", 'callback_data' => '/achievements_all ' . ($page + 1)]);
        }

        if (count($achievements) > 0 && count($achievements) < 5) {
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => '/achievements_all ' . ($page - 1)]);
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
$botman->hears('/achievements_my ([0-9]+)', function ($bot, $page) {
    try {
        $telegramUser = $bot->getUser();

        $id = $telegramUser->getId();

        $user = \App\User::with(["achievements"])->where("telegram_chat_id", $id)->first();


        if (count($user->achievements) > 0) {
            $achievements = $user->achievements()
                ->skip($page * 5)
                ->take(5)
                ->orderBy('id', 'DESC')
                ->get();

            foreach ($achievements as $key => $achievement) {
                $message = Question::create(($achievement->activated == 0 ? "" : "\xE2\x9C\x85") . ($achievement->title ?? "Без названия [#" . $achievement->id . "]"))
                    ->addButtons([Button::create("Подробнее")
                        ->value("/achievements_description " . $achievement->id)]);
                $bot->reply($message, ["parse_mode" => "Markdown"]);
            }
        } else
            $bot->reply("У вас еще активированных нет достижений!", ["parse_mode" => "Markdown"]);

        $inline_keyboard = [];
        if ($page == 0 && count($user->achievements) == 5)
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xA9Далее", 'callback_data' => '/achievements_my ' . ($page + 1)]);

        if ($page > 0) {
            if (count($user->achievements) == 0) {
                array_push($inline_keyboard, ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => '/achievements_my ' . ($page - 1)]);
            }

            if (count($user->achievements) == 5) {
                array_push($inline_keyboard, ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => '/achievements_my ' . ($page - 1)]);
                array_push($inline_keyboard, ['text' => "\xE2\x8F\xA9Далее", 'callback_data' => '/achievements_my ' . ($page + 1)]);
            }

            if (count($user->achievements) > 0 && count($user->achievements) < 5) {
                array_push($inline_keyboard, ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => '/achievements_my ' . ($page - 1)]);
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

    } catch (Exception $e) {
        $bot->reply($e->getMessage() . " " . $e->getLine());
    }
});
$botman->hears('/achievements_description ([0-9]+)', function ($bot, $achievementId) {

    $achievement = \App\Achievement::find($achievementId);

    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();
    $user = \App\User::with(["achievements"])
        ->where("telegram_chat_id", $id)
        ->first();

    $stat = \App\Stat::where("user_id", "=", $user->id, 'and')
        ->where("stat_type", "=", $achievement->trigger_type->value)
        ->first();

    if ($achievement == null) {
        $bot->reply("Достижение не найдено!", ["parse_mode" => "Markdown"]);
        return;
    }


    $currentVal = $stat == null ? 0 : $stat->stat_value;

    $progress = $currentVal >= $achievement->trigger_value ?
        "\n*Успешно выполнено!*" :
        "Прогресс:*" . $currentVal . "* из *" . $achievement->trigger_value . "*";

    $attachment = new Image($achievement->ach_image_url);
    $message = OutgoingMessage::create(
        "*" .
        $achievement->title . "*\n_" .
        $achievement->description . "_\n" .
        $progress
    )
        ->withAttachment($attachment);

    $bot->reply($message, ["parse_mode" => "Markdown"]);

    $attachment = new Image($achievement->prize_image_url);
    $message = OutgoingMessage::create(
        "*\xF0\x9F\x91\x86Ваш приз:*\n_" .
        $achievement->prize_description . "_"
    )
        ->withAttachment($attachment);

    $bot->reply($message, ["parse_mode" => "Markdown"]);

    $on_ach_activated = $user->achievements()
        ->where("achievement_id", "=", $achievementId)
        ->first();


    $btn_tmp = [];

    if ($on_ach_activated)
        if ($on_ach_activated->activated == false)
            array_push($btn_tmp, Button::create("\xF0\x9F\x8E\x81Получить приз")->value("/achievements_get_prize $achievementId"));
    array_push($btn_tmp, Button::create("\xE2\x8F\xAAВернуться назад")->value("/achievements_panel"));

    $message = Question::create("Дальнейшие действия")
        ->addButtons($btn_tmp);

    $bot->reply($message, ["parse_mode" => "Markdown"]);


});
$botman->hears('/achievements_get_prize ([0-9]+)', function ($bot, $achievementId) {
    $achievement = \App\Achievement::find($achievementId);
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();
    $user = \App\User::with(["achievements"])->where("telegram_chat_id", $id)->first();

    $on_ach_activated = $user->achievements()
        ->where("achievement_id", "=", $achievementId)
        ->first();


    if ($on_ach_activated->activated == true) {
        $bot->reply("Вы уже получили приз за данное достижение!", ["parse_mode" => "Markdown"]);
        return;
    }

    $stat = \App\Stat::where("user_id", "=", $user->id, 'and')
        ->where("stat_type", "=", $achievement->trigger_type->value)
        ->first();
    $currentVal = $stat == null ? 0 : $stat->stat_value;

    if ($currentVal >= $achievement->trigger_value) {
        $tmp_id = "$id";
        while (strlen($tmp_id) < 10)
            $tmp_id = "0" . $tmp_id;

        $tmp_achievement_id = (string)$achievement->id;
        while (strlen($tmp_achievement_id) < 10)
            $tmp_achievement_id = "0" . $tmp_achievement_id;

        $code = base64_encode("012" . $tmp_id . $tmp_achievement_id);

        $attachment = new Image(env("QR_URL")."https://t.me/" . env("APP_BOT_NAME") . "?start=$code");
        //$attachment = new Image(env("APP_URL") . "/image/?data=" . base64_encode("https://t.me/" . env("APP_BOT_NAME") . "?start=$code"));

        $message = OutgoingMessage::create('_Код для активации достижения_')
            ->withAttachment($attachment);

        $bot->reply($message, ["parse_mode" => "Markdown"]);
    } else
        $bot->reply("Вы не можете получить приз за данное достижение", ["parse_mode" => "Markdown"]);


});
$botman->hears('/help', function ($bot) {
    $articles = Article::where("part", 6)
        ->where("is_visible", 1)
        ->get();

    if (count($articles) > 0)
        foreach ($articles as $article)
            $bot->reply($article->url, ["parse_mode" => "Markdown"]);
    else
        $bot->reply("Статьи не найдены", ["parse_mode" => "Markdown"]);
});
$botman->hears('/rules', function ($bot) {
    $articles = Article::where("part", 2)
        ->where("is_visible", 1)
        ->get();

    if (count($articles) > 0)
        foreach ($articles as $article)
            $bot->reply($article->url, ["parse_mode" => "Markdown"]);
    else
        $bot->reply("Статьи не найдены", ["parse_mode" => "Markdown"]);
});
$botman->hears('/dev', function ($bot) {
    $articles = Article::where("part", 5)
        ->where("is_visible", 1)
        ->get();

    if (count($articles) > 0)
        foreach ($articles as $article)
            $bot->reply($article->url, ["parse_mode" => "Markdown"]);
    else
        $bot->reply("Статьи не найдены", ["parse_mode" => "Markdown"]);

});
$botman->hears('/about', function ($bot) {
    $articles = Article::where("part", 3)
        ->where("is_visible", 1)
        ->get();

    if (count($articles) > 0)
        foreach ($articles as $article)
            $bot->reply($article->url, ["parse_mode" => "Markdown"]);
    else
        $bot->reply("Статьи не найдены", ["parse_mode" => "Markdown"]);

});
$botman->hears('/promouter', function ($bot) {
    $telegramUser = $bot->getUser();

    $id = $telegramUser->getId();

    $user = \App\User::where("telegram_chat_id", $id)->first();

    if (!$user) {
        $bot->startConversation(new StartConversation($bot));
        return;
    }
    $tmp_id = "$id";
    while (strlen($tmp_id) < 10)
        $tmp_id = "0" . $tmp_id;


    $keyboard2 = [
        'inline_keyboard' => [
            [
                ['text' => "Telegram", 'callback_data' => "/ref 1"],
                ['text' => "Vkontakte", 'url' => "https://vk.com/share.php?url=" .
                    "https://t.me/" . env("APP_BOT_NAME") . "?start=" . base64_encode("004" . $tmp_id . "0000000000") .
                    "&title=Делись ссылкой с друзьями и получай бонусы!"

                ],

            ],
            [
                ['text' => "Facebook", 'url' => "http://www.facebook.com/sharer.php?u=" .
                    "https://t.me/" . env("APP_BOT_NAME") . "?start=" . base64_encode("005" . $tmp_id . "0000000000")

                ],
                ['text' => "Intagram", 'callback_data' => "/ref 4"],

            ],

            [
                ['text' => "Статистика активности", 'callback_data' => "/activity_information"],
            ],


        ]
    ];

    $bot->sendRequest("sendMessage",
        [
            "text" => '_Вы хотите быть промоутером? Тогда этот раздел именно для вас! Выбирайте соц. сеть, делитесь ссылкой из сообщения и накапливайте бонусы! Вы также можете просматривать полную статистику по своему аккаунту._',
            "parse_mode" => "Markdown",
            'reply_markup' => json_encode($keyboard2)
        ]);

    $articles = Article::where("part", 1)
        ->where("is_visible", 1)
        ->get();

    foreach ($articles as $article)
        $bot->reply($article->url, ["parse_mode" => "Markdown"]);

});
$botman->hears('/suppliers', function ($bot) {
    $articles = Article::where("part", 7)
        ->where("is_visible", 1)
        ->get();

    if (count($articles) > 0)
        foreach ($articles as $article)
            $bot->reply($article->url, ["parse_mode" => "Markdown"]);
    else
        $bot->reply("Статьи не найдены", ["parse_mode" => "Markdown"]);
});
$botman->hears('/activity_information', function ($bot) {

    $stat_types = [
        "\xE2\x96\xAAКоличество активаций приза по акции: *%d* раз.\n",
        "\xE2\x96\xAAКоличество рефералов:  *%d* человек.\n",
        "\xE2\x96\xAAМаксимальное количество накопленного CashBack: *%d* руб.\n",
        "\xE2\x96\xAAКоличество переходов из ВК: *%d* раз.\n",
        "\xE2\x96\xAAКоличество переходов из Facebook: *%d* раз.\n",
        "\xE2\x96\xAAКоличество переходов из Instagram: *%d* раз.\n",
        "\xE2\x96\xAAКоличество переходов из других источников: *%d* раз.\n",
        "\xE2\x96\xAAМасимальный реферальный бонус: *%d* руб.\n",
        "\xE2\x96\xAAКоличество активированных достижений: *%d* ед.\n",
        "\xE2\x96\xAAМаксимальное количество списанного CashBack: *%d* руб.\n",
    ];

    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();
    $user = \App\User::where("telegram_chat_id", $id)->first();

    $stats = \App\Stat::where("user_id", $user->id)
        ->get();

    $message = "";

    foreach ($stats as $stat)
        $message .= sprintf($stat_types[$stat->stat_type->value], $stat->stat_value);

    $bot->reply(count($stats) > 0 ? $message : "Статистика еще не ведется для вашего аккаунта!", ["parse_mode" => "Markdown"]);

});
$botman->hears('/articles ([0-9]+)', function ($bot, $page) {


    $articles = Article::where("part", 0)
        ->where("is_visible", 1)
        ->skip($page * 5)
        ->take(5)
        ->orderBy('id', 'DESC')
        ->get();

    if (count($articles) > 0) {
        foreach ($articles as $article)
            $bot->reply($article->url, ["parse_mode" => "Markdown"]);
    } else
        $bot->reply("Статьи появтяся в скором времени!", ["parse_mode" => "Markdown"]);

    $inline_keyboard = [];
    if ($page == 0 && count($articles) == 5)
        array_push($inline_keyboard, ['text' => "\xE2\x8F\xA9Далее", 'callback_data' => '/articles ' . ($page + 1)]);

    if ($page > 0) {
        if (count($articles) == 0) {
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => '/articles ' . ($page - 1)]);
        }

        if (count($articles) == 5) {
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => '/articles ' . ($page - 1)]);
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xA9Далее", 'callback_data' => '/articles ' . ($page + 1)]);
        }

        if (count($articles) > 0 && count($articles) < 5) {
            array_push($inline_keyboard, ['text' => "\xE2\x8F\xAAНазад", 'callback_data' => '/articles ' . ($page - 1)]);
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
$botman->hears('/check_lottery_slot ([0-9]+) ([0-9]+)', function ($bot, $slotId,$codeId) {
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();
    $prize = Prize::with(["company"])
        ->where("id",$slotId)
        ->first()??null;

    if ($prize==null) {
        $bot->reply("Увы, что-то пошло не так и приза нет:(");
        return;
    }

    if ($prize->current_activation_count==$prize->summary_activation_count){
        $bot->reply("Увы, к данному моменту все призы закончились");
        return;
    }

    $message = "*" . $prize->title . "*\n"
        . "_" . $prize->description . "_\n";

    $prize->current_activation_count++;
    $prize->updated_at = Carbon::now();
    $prize->save();

    $code = \App\Promocode::find($codeId);
    $code->prize_id = $prize->id;
    $code->updated_at = Carbon::now();
    $code->save();

    $bot->sendRequest("sendPhoto",
        [
            "chat_id" => "$id",
            "photo" => $prize->image_url,
            "caption" => $message,
            "parse_mode" => "Markdown",
        ]);
    $user = User::where("telegram_chat_id", $id)->first();

    $companyTitle = $prize->company->title;

    $message = "*Пользователь поучаствовал в розыгрыше и выиграл*\n$message"
        . "*Дата участия*:" . (Carbon::now()) . "\n";
    try {
        Telegram::sendMessage([
            'chat_id' => env("CHANNEL_ID"),
            'parse_mode' => 'Markdown',
            'text' => $message,
            'disable_notification' => 'true'
        ]);
    } catch (\Exception $e) {

    }
});

$botman->fallback(function ($bot) {
    Log::info("fallback");

    $bot->loadDriver(TelegramInlineQueryDriver::DRIVER_NAME);


    $queryObject = json_decode($bot->getDriver()->getEvent());

    if ($queryObject) {

        $id = $queryObject->from->id;

        $promotions = \App\Promotion::all();
        $button_list = [];
        foreach ($promotions as $promo) {


            $time_0 = (date_timestamp_get(new DateTime($promo->start_at)));
            $time_1 = (date_timestamp_get(new DateTime($promo->end_at)));

            $time_2 = date_timestamp_get(now());


            if ($time_2 >= $time_0 && $time_2 < $time_1) {

                $tmp_id = "$id";
                while (strlen($tmp_id) < 10)
                    $tmp_id = "0" . $tmp_id;

                $tmp_promo_id = (string)$promo->id;
                while (strlen($tmp_promo_id) < 10)
                    $tmp_promo_id = "0" . $tmp_promo_id;

                $code = base64_encode("001" . $tmp_id . $tmp_promo_id);
                $url_link = "https://t.me/" . env("APP_BOT_NAME") . "?start=$code";
                Log::info($url_link);
                Log::info("001" . $tmp_id . $tmp_promo_id);
                $tmp_button = [
                    'type' => 'article',
                    'id' => uniqid(),
                    'title' => $promo->title,
                    'input_message_content' => [
                        'message_text' => $promo->description . "\n" . $promo->promo_image_url,
                    ],
                    'reply_markup' => [
                        'inline_keyboard' => [
                            [
                                ['text' => "Ссылка на акцию", "url" => "$url_link"],
                            ],
                            [
                                ['text' => "Отправить другу", "switch_inline_query" => ""],
                            ]
                        ]
                    ],
                    'thumb_url' => $promo->promo_image_url,
                    'url' => "https://vk.com/lotus",
                    'description' => $promo->description,
                    'hide_url' => true

                ];

                array_push($button_list, $tmp_button);


            }
        }
        return $bot->sendRequest("answerInlineQuery",
            [
                'cache_time' => 0,
                "inline_query_id" => json_decode($bot->getEvent())->id,
                "results" => json_encode($button_list)
            ]);
    }
});

