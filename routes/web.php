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
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;
use Trello\Client;
use Trello\Manager;
use Vinkla\Instagram\Instagram;


Route::post('/instawebhook', function (Request $request) {

    Log::info("test post");

});

Route::get('/instawebhook', function (Request $request) {

    Log::info("test");
    // Create the card
    /*
        $client = new Client();
        $client->authenticate(config("trello.api_key"), config("trello.api_token"), Client::AUTH_URL_CLIENT_ID);

        $manager = new Manager($client);

        $card = $manager->getCard("");

        $card
            ->setName('Test card 2')
            ->setDescription('Test description 2')
            ->save();

        //dd($boards
        */
    Log::info($request->get("hub_challenge"));
    return $request->get("hub_challenge");
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

    Route::get('/', 'HomeController@index')
        ->name('home');

    Route::post('/', 'HomeController@index')
        ->name('home.qr');

    Route::get('/lang', 'HomeController@translations')
        ->name('lang');


    Route::post('/search', 'HomeController@search')
        ->name('users.phone.search');

    Route::get('/search_ajax/', 'HomeController@searchAjax')
        ->name('users.ajax.search');


    Route::post('/announce', 'HomeController@announce')
        ->name('users.announce');


    Route::get('/sender', 'HomeController@sender');

    Route::post('/sender', 'HomeController@announceCustom')
        ->name("sender.announce");


    Route::post("/users/cashback/add", "HomeController@cashback")
        ->name("users.cashback.add");


    Route::get("/users/show/byPhone/{phone}", "UsersController@showByPhone")
        ->name("users.show.phone");


    Route::get("/users/cashback/{id}", "UsersController@cashBackPage")->name("users.cashback.index");

    Route::any('users/search', 'UsersController@search')->name("users.search");

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
    ]);

    Route::get("/promotions/copy/{id}", "PromotionController@copy")->name("promotions.copy");
    Route::get("/promotions/channel/{id}", "PromotionController@channel")->name("promotions.channel");
    Route::get("/promotions/incategory/{id}", "PromotionController@inCategory")->name("promotions.in_category");
    Route::get("/promotions/incompany/{id}", "PromotionController@inCompany")->name("promotions.in_company");

    Route::get("/users/promotions/{id}", "UsersController@getUserPromotions")->name("users.promotions");

    Route::get("/events/channel/{id}", "EventsController@channel")->name("events.channel");
    Route::get("/companies/channel/{id}", "CompanyController@channel")->name("companies.channel");
    Route::get("/companies/hide/{id}", "CompanyController@hide")->name("companies.hide");
    Route::get("/achievements/channel/{id}", "AchievementsController@channel")->name("achievements.channel");
    Route::get("/prizes/channel/{id}", "PrizeController@channel")->name("prizes.channel");

    Route::get("/instapromos/channel/{id}", "InstaPromotionController@channel")->name("instapromos.channel");
    Route::get("/instapromos/duplication/{id}", "InstaPromotionController@duplication")->name("instapromos.duplication");
    Route::get("/instapromos/upoadphotos", "InstaPromotionController@uploadphotos")->name("users.uploadphotos");

    Route::get("/promocodes/change_status/{id}", "PromocodeController@change_status")->name("promocodes.changestatus");

    Route::get("/prizes/duplication/{id}", "PrizeController@duplication")->name("prizes.duplication");


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

