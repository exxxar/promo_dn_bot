<?php

use App\Http\Controllers\BotManController;
use App\RefferalsPaymentHistory;
use App\User;
use App\UserHasPromo;
use BotMan\BotMan\Messages\Attachments\File;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

$botman = resolve('botman');

$botman->hears('/start ([0-9a-zA-Z]+)', BotManController::class . '@startDataConversation');
$botman->hears('/start', BotManController::class . '@startConversation');
$botman->hears('/promotion ([0-9]+)', BotManController::class . '@promoConversation');
$botman->hears('/payment ([0-9]{1,10})', BotManController::class . '@paymentConversation');


$botman->hears("\xE2\x9B\x84Новый год", function ($bot) {
    $keyboard = [
        'inline_keyboard' => [
            [['text' => 'Посмотреть призы!', 'url' => env("APP_PROMO_LINK")]
            ]
        ]
    ];

    $bot->sendRequest("sendMessage",
        ["text" => 'Мы готовим Вам на Новый год крутые подарки! Посмотри!', 'reply_markup' => json_encode($keyboard)
        ]);
});
$botman->hears("\xF0\x9F\x93\xB2QR", function ($bot) {
    $telegramUser = $bot->getUser();

    $id = $telegramUser->getId();

    $user = \App\User::where("telegram_chat_id", $id)->first();

    $ref = $user->referrals_count;

    $bot->reply("Вы пригласили *$ref* друзей!\n_Делитесь Вашим QR-кодом и накапливайте баллы!_\n", ["parse_mode" => "Markdown"]);

    $tmp_id = "$id";
    while (strlen($tmp_id) < 10)
        $tmp_id = "0" . $tmp_id;

    $code = base64_encode("001" . $tmp_id . "000000000");
    $tmp_img = substr($code, 0, strlen($code) - 2);
    $bot->reply(env("APP_URL") . "/image/" . $tmp_img,
        ["parse_mode" => "Markdown"]);
});
$botman->hears("\xF0\x9F\x92\xB3Баллы", function ($bot) {
    $telegramUser = $bot->getUser();

    $id = $telegramUser->getId();

    $user = \App\User::where("telegram_chat_id", $id)->first();

    if ($user != null) {
        $summary = $user->referral_bonus_count + $user->cashback_bonus_count;

        $bot->reply("У вас *$summary* баллов!\n_Для оплаты дайте отсканировать данный QR-код сотруднику!_\n", ["parse_mode" => "Markdown"]);

        $tmp_id = "$id";
        while (strlen($tmp_id) < 10)
            $tmp_id = "0" . $tmp_id;

        $code = base64_encode("002" . $tmp_id . "000000000");
        $tmp_img = substr($code, 0, strlen($code) - 2);
        $bot->reply(env("APP_URL") . "/image/" . $tmp_img,
            ["parse_mode" => "Markdown"]);
    }

});
$botman->hears("\xF0\x9F\x92\xB0Кэшбэк", function ($bot) {
    $telegramUser = $bot->getUser();

    $id = $telegramUser->getId();

    $user = \App\User::where("telegram_chat_id", $id)->first();

    $cashback = $user->cashback_bonus_count;

    $bot->reply("У вас накопилось *$cashback* рублей кэшбэка!",
        ["parse_mode" => "Markdown"]);


});
$botman->hears("\xF0\x9F\x94\xA5Все категории", function ($bot) {
    $categories = \App\Category::all();

    $tmp = [];

    foreach ($categories as $cat) {
        array_push($tmp, Button::create($cat->title)->value("/category " . $cat->id));
    }

    $message = Question::create("Акции по категориям:")
        ->addButtons($tmp);


    $bot->reply($message);
});
$botman->hears("\xF0\x9F\x94\xA5Все компании", function ($bot) {
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
            [['text' => 'Перейти на сайт!', 'url' => env("APP_PROMO_LINK")]
            ]
        ]
    ];

    $bot->sendRequest("sendMessage",
        ["text" => 'Посмотрите список всех акций на нашем сайте!', 'reply_markup' => json_encode($keyboard)
        ]);


});

$botman->hears('stop', function ($bot) {
    $bot->reply('stopped');
})->stopsConversation();

$botman->hears('/category ([0-9]+)', function ($bot, $category_id) {

    $promotions = \App\Promotion::with(["users"])->where("category_id", "=", $category_id)
        ->get();

    $tmp = [];

    foreach ($promotions as $promo) {

        $telegramUser = $bot->getUser();
        $id = $telegramUser->getId();

        $on_promo = $promo->users()->where('telegram_chat_id', "$id")->first();

        if ($on_promo == null)
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
    $message = OutgoingMessage::create($company->title."\n_".$company->description."_")
        ->withAttachment($attachment);

    // Reply message object
    $bot->reply($message,["parse_mode" => "Markdown"]);

    $promotions = \App\Promotion::with(["users"])->where("company_id", "=", $company_id)
        ->get();

    $tmp = [];

    foreach ($promotions as $promo) {

        $telegramUser = $bot->getUser();
        $id = $telegramUser->getId();

        $on_promo = $promo->users()->where('telegram_chat_id', "$id")->first();

        if ($on_promo == null)
            array_push($tmp, Button::create($promo->title)->value("/promotion " . $promo->id));
    }


    if (count($tmp) > 0) {
        $message = Question::create("Акции от компании:")
            ->addButtons($tmp);


        $bot->reply($message);
    } else
        $bot->reply("Акций от компании не найдено или все акции данной компании собраны:(");
});


$botman->hears('/payment_accept ([0-9]{1,10}) ([0-9]{3,10})', function ($bot, $value, $user_id) {

    $telegramUser = $this->bot->getUser();
    $id = $telegramUser->getId();

    $user = User::where("telegram_chat_id", $id)
        ->first();

    if ($user->referral_bonus_count + $user->cashback_bonus_count > intval($value)) {


        RefferalsPaymentHistory::create([
            'user_id' => $user->id,
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

$botman->hears('/payment_decline ([0-9]{1,10}) ([0-9]{3,10})', function ($bot, $value, $user_id) {
    $bot->reply('Оплата отклонена');

    $this->bot->sendRequest("sendMessage",
        [
            "text" => 'Пользователь отклонил оплату',
            "chat_id" => $user_id,
        ]);

});
