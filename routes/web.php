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
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
    return view('welcome');
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
        ->name('users.search');

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

    Route::resources([
        'users' => 'UsersController',
        'categories' => 'CategoryController',
        'companies' => 'CompanyController',
        'promotions' => 'PromotionController',
        'cashback' => 'CashbackHistoryController',
        'refferals' => 'RefferalsHistoryController',
        'payments' => 'RefferalsPaymentHistoryController',
        'events' => 'EventsController',
        'achievements' => 'AchievementsController',
    ]);

});






Route::get("/image/{data}",function ($data){

    $tmp_data = base64_decode($data);
    $pngImage = QrCode::format('png')->merge('https://psv4.userapi.com/c848220/u138206920/docs/d16/6f7adde4f371/bot.png', 0.3, true)
        ->size(200)->errorCorrection('H')
        ->generate($tmp_data);

    return response($pngImage)->header('Content-type','image/png');
});

