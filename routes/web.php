<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Events\NetworkCashBackEvent;
use App\Events\NetworkLevelRecounterEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;
use Trello\Client;
use Trello\Manager;
use Vinkla\Instagram\Instagram;

Route::get("/artest", function () {
   return view("ar");
});

Route::get("/dotest", function () {
    $keyboard = [
        [
            ["text" => "test btn 0", "callback_data" => "/fff 0"]
        ]
    ];
    Telegram::sendMessage([
        'chat_id' => env("CHANNEL_ID"),
        'parse_mode' => 'Markdown',
        'text' => "test",
        'disable_notification' => 'true',
        'reply_markup' => json_encode([
            'inline_keyboard' => $keyboard,
        ])
    ]);
    /* sleep(10);
     Telegram::editMessageText([
         'text'=>"do",
         'chat_id' => env("CHANNEL_ID"),
         "message_id"=>$test["message_id"]
     ]);
     $keyboard = [
         [
             ["text"=>"test btn 2","callback_data"=>"/fff"]
         ]
     ];
     Telegram::editMessageReplyMarkup([
         'chat_id' => env("CHANNEL_ID"),
         "message_id"=>$test["message_id"],
         'reply_markup' => json_encode([
             'inline_keyboard' => $keyboard,
         ])
     ]);


     Log::info(print_r($test,true));*/
});
Route::match(['get', 'post'], '/botman', 'BotManController@handle');

Auth::routes();

Route::get('auth/telegram/callback', 'TelegramAuthController@handleTelegramCallback')->name('auth.telegram.handle');

Route::get('/', 'WelcomeController@index')->name("welcome");

Route::get('/terms-of-use', 'WelcomeController@terms')->name("terms");
Route::get('/privacy-policy', 'WelcomeController@policy')->name("policy");

Route::get("/insta", 'HomeController@instagramCallabck');
Route::post('/send-request', 'WelcomeController@sendRequestFromSite')->name("send.callback");

Route::prefix('admin')->group(function () {
    Route::get('/', 'HomeController@index')->name('home');
    Route::post('/', 'HomeController@index')->name('home.qr');
    Route::get('/lang', 'HomeController@translations')->name('lang');
    Route::get('/sender', 'HomeController@sender');
    Route::post('/sender', 'HomeController@announceCustom')->name("sender.announce");
    Route::get("/promocodes/change_status/{id}", "PromocodeController@change_status")->name("promocodes.changestatus");

    Route::name('users.')->prefix('users')->group(function () {
        Route::get("/promotions/{id}", "UsersController@getUserPromotions")->name("promotions");
        Route::get("/cashback/{id}", "UsersController@cashBackPage")->name("cashback.index");
        Route::any('/search', 'UsersController@search')->name("search");
        Route::post('/search', 'HomeController@search')->name('phone.search');
        Route::get("/show/byPhone/{phone}", "UsersController@showByPhone")->name("show.phone");
        Route::post("/cashback/add", "HomeController@cashback")->name("cashback.add");
        Route::post('/announce', 'HomeController@announce')->name('announce');
        Route::get('/search_ajax', 'HomeController@searchAjax')->name('ajax.search');

        Route::name('uploadphotos.')->prefix('uploadphotos')->group(function () {
            Route::get("/", "InstaPromotionController@uploadphotos")->name("index");
            Route::post("/accept/{id}", "InstaPromotionController@accept")->name("accept");
            Route::get("/decline/{id}", "InstaPromotionController@decline")->name("decline");
        });
    });

    Route::name('promotions.')->prefix('promotions')->group(function () {
        Route::get("/copy/{id}", "PromotionController@copy")->name("copy");
        Route::get("/channel/{id}", "PromotionController@channel")->name("channel");
        Route::get("/incategory/{id}", "PromotionController@inCategory")->name("in_category");
        Route::get("/incompany/{id}", "PromotionController@inCompany")->name("in_company");
    });

    Route::name('instapromos.')->prefix('instapromos')->group(function () {
        Route::get("/channel/{id}", "InstaPromotionController@channel")->name("channel");
        Route::get("/duplication/{id}", "InstaPromotionController@duplication")->name("duplication");
        Route::get("/userson/{id}", "InstaPromotionController@usersOn")->name("userson");
    });


    Route::name('geo_quests.')->prefix('geo_quests')->group(function () {
        Route::get("/channel/{id}", "GeoQuestController@channel")->name("channel");
        Route::get("/duplication/{id}", "GeoQuestController@duplication")->name("duplication");
        Route::get("/points/append/{id}", "GeoQuestController@append")->name("points.append");
        Route::post("/points/store/{id}", "GeoQuestController@storePoints")->name("points.store");
    });

    Route::name('geo_positions.')->prefix('geo_positions')->group(function () {
        Route::get("/channel/{id}", "GeoQuestController@channel")->name("channel");
        Route::get("/duplication/{id}", "GeoQuestController@duplication")->name("duplication");
    });


    Route::name('prizes.')->prefix('prizes')->group(function () {
        Route::get("/channel/{id}", "PrizeController@channel")->name("channel");
        Route::get("/duplication/{id}", "PrizeController@duplication")->name("duplication");
    });

    Route::name('charities.')->prefix('charities')->group(function () {
        Route::get("/channel/{id}", "CharityController@channel")->name("channel");
        Route::get("/duplication/{id}", "CharityController@duplication")->name("duplication");
        Route::get("/userson/{id}", "CharityController@usersOn")->name("userson");
    });

    Route::name('companies.')->prefix('companies')->group(function () {
        Route::get("/channel/{id}", "CompanyController@channel")->name("channel");
        Route::get("/hide/{id}", "CompanyController@hide")->name("hide");
    });

    Route::name('events.')->prefix('events')->group(function () {
        Route::get("/channel/{id}", "EventsController@channel")->name("channel");
    });

    Route::name('achievements.')->prefix('achievements')->group(function () {
        Route::get("/channel/{id}", "AchievementsController@channel")->name("channel");
    });

    Route::name('bot_hubs.')->prefix('bot_hubs')->group(function () {
        Route::get("/set_webhook/{id}", "BotHubController@setWebHook")->name("webhook");
    });


    Route::resources([
        'articles' => 'ArticleController',
        'users' => 'UsersController',
        'categories' => 'CategoryController',
        'companies' => 'CompanyController',
        'promotions' => 'PromotionController',
        'cashback' => 'CashbackHistoryController',
        'refferals' => 'RefferalsHistoryController',
        'payments' => 'RefferalsPaymentHistoryController',
        'events' => 'EventsController',
        'achievements' => 'AchievementsController',
        'prizes' => 'PrizeController',
        'promocodes' => 'PromocodeController',
        'instapromos' => 'InstaPromotionController',
        'cashbackinfos' => 'CashBackInfoController',
        'charities' => 'CharityController',
        'charityhistories' => 'CharityHistoryController',
        'geo_quests' => 'GeoQuestController',
        'geo_positions' => 'GeoPositionController',
        'geo_histories' => 'GeoHistoryController',
        'bot_hubs' => 'BotHubController',
    ]);
});


Route::get("/image", function (\Illuminate\Http\Request $request) {
    try {
        $tmp_data = base64_decode($request->get("data"));
    } catch (Exception $e) {
        $tmp_data = $request->get("data");
    }

    try {
        $pngImage = QrCode::format('png')->merge(env("APP_URL") . 'bot.png', 0.3, true)
            ->size(500)->errorCorrection('H')
            ->generate($tmp_data);

        return response($pngImage)->header('Content-type', 'image/png');
    } catch (Exception $e) {
        return "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://t.me/" . env("APP_BOT_NAME");
    }
});

