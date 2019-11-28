<?php

use App\Event;
use App\Http\Controllers\BotManController;
use App\Promotion;
use App\RefferalsPaymentHistory;
use App\User;
use App\UserHasPromo;
use BotMan\BotMan\Messages\Attachments\File;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

$botman = resolve('botman');

$botman->hears('Попробовать снова', BotManController::class . '@startConversation');
$botman->hears('/start', BotManController::class . '@startConversation');

$botman->hears('/start ([0-9a-zA-Z=]+)', BotManController::class . '@startDataConversation');

$botman->hears('/promotion ([0-9]+)', BotManController::class . '@promoConversation');
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
        ["text" => 'Мы готовим Вам на Новый год крутые подарки! Посмотри!', 'reply_markup' => json_encode($keyboard)
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

    $code = base64_encode("001" . $tmp_id . "000000000");

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
        $payments = RefferalsPaymentHistory::where("user_id", $user->id)
            ->get();

        if (count($payments) > 0) {
            $message = Question::create($tmp_message)
                ->addButtons([
                    Button::create("Посмотреть мои расходы")->value("/payments 0")
                ]);

            $bot->reply($message, ["parse_mode" => "Markdown"]);
        } else
            $bot->reply($tmp_message, ["parse_mode" => "Markdown"]);


        $tmp_id = "$id";
        while (strlen($tmp_id) < 10)
            $tmp_id = "0" . $tmp_id;

        $code = base64_encode("002" . $tmp_id . "000000000");


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

        if ($on_promo == null && $time_2 >= $time_0 && $time_2 < $time_1)
            array_push($tmp, Button::create($promo->title)->value("/promotion " . $promo->id));
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

        if ($on_promo == null && $time_2 >= $time_0 && $time_2 < $time_1)
            array_push($tmp, Button::create($promo->title)->value("/promotion " . $promo->id));
    }


    if (count($tmp) > 0) {
        $message = Question::create("Акции от компании:")
            ->addButtons($tmp);


        $bot->reply($message);
    } else
        $bot->reply("Акций от компании не найдено или все акции данной компании собраны:(");
});


$botman->hears('/payment_accept ([0-9]{1,10}) ([0-9]{3,10}) ([0-9]+)', function ($bot, $value, $user_id, $company_id) {

    $telegramUser = $this->bot->getUser();
    $id = $telegramUser->getId();

    $user = User::where("telegram_chat_id", $id)
        ->first();

    if ($user->referral_bonus_count + $user->cashback_bonus_count > intval($value)) {


        RefferalsPaymentHistory::create([
            'user_id' => $user->id,
            'company_id' => $company_id,
            'employee_id' => (User::where("telegram_chat_id", $user_id)->first())->id,
            'value' => intval($value),
        ]);

        if ($user->referral_bonus_count <= intval($value)) {
            $module = intval($value) - $user->referral_bonus_count;
            $user->referral_bonus_count = 0;
            $user->cashback_bonus_count -= $module;
        } else
            $user->referral_bonus_count -= intval($value);

        $user->save();

        $this->bot->sendRequest("sendMessage",
            [
                "text" => 'Пользователь подтвердил оплату',
                "chat_id" => $user_id,
            ]);

    } else {
        $this->bot->sendRequest("sendMessage",
            [
                "text" => 'У пользователя недостаточно бонусных баллов!',
                "chat_id" => $user_id,
            ]);
    }
});

$botman->hears('/payment_decline ([0-9]{1,10}) ([0-9]{3,10}) ([0-9]+)', function ($bot, $value, $user_id, $company_id) {
    $bot->reply('Оплата отклонена');

    $this->bot->sendRequest("sendMessage",
        [
            "text" => 'Пользователь отклонил оплату',
            "chat_id" => $user_id,
        ]);

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

$botman->hears('/events ([0-9]+)', function ($bot, $page) {


    $events = Event::skip($page * 5)
        ->take(5)
        ->orderBy('id', 'DESC')
        ->get();

    foreach ($events as $key => $event) {

        $attachment = new Image($event->event_image_url);
        $message = OutgoingMessage::create("*" . $event->title . "*\n" . $event->description)
            ->withAttachment($attachment);

        $bot->reply($message, ["parse_mode" => "Markdown"]);
    }

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

