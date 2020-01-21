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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

Route::get('/test_crash',function (){
    throw new \PHPUnit\Runner\Exception("asdasd");
});
Route::get('/test_user', function () {
    /*  $users = \App\User::with(["parent","childs"])->get();
      foreach($users as $user)
          foreach ($user->childs as $key=>$u) {
              echo ($key + 1) . ")" .$user->name." child=". $u->name . "<br>";
               foreach ($u->childs as $u1)
                   echo $u1->name;
          }*/
    //event(new NetworkLevelRecounterEvent(10));
    //event(new NetworkCashBackEvent(6,100));
    $user = \App\User::find(10);
    event(new \App\Events\AchievementEvent(3, 150, $user));

});
Route::get('/cabinet', 'HomeController@cabinet');


Route::get('/test_get_updates', 'BotManController@testGetUpdates');


Route::get('/', function (Request $request) {
    $companies = \App\Company::with(["promotions","promotions.category"])->get();

    return view('welcome',compact("companies"));
});


Route::match(['get', 'post'], '/botman', 'BotManController@handle');
Route::get('/botman/tinker', 'BotManController@tinker');

Auth::routes();

Route::get('auth/telegram/callback', 'TelegramAuthController@handleTelegramCallback')->name('auth.telegram.handle');


Route::prefix('admin')->group(function () {

    Route::get('/', 'HomeController@index')
        ->name('home');

    Route::post('/', 'HomeController@index')
        ->name('home.qr');


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
    ]);

    Route::get("/promotions/copy/{id}","PromotionController@copy")->name("promotions.copy");
    Route::get("/promotions/channel/{id}","PromotionController@channel")->name("promotions.channel");
    Route::get("/events/channel/{id}","EventsController@channel")->name("events.channel");
    Route::get("/companies/channel/{id}","CompanyController@channel")->name("companies.channel");
    Route::get("/achievements/channel/{id}","AchievementsController@channel")->name("achievements.channel");
    Route::get("/prizes/channel/{id}","PrizeController@channel")->name("prizes.channel");
    Route::get("/promocodes/change_status/{id}","PromocodeController@change_status")->name("promocodes.changestatus");

    Route::get("/duplication/channel/{id}","PrizeController@duplication")->name("prizes.duplication");

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

