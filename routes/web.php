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

use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

Route::get('/', function () {
    return view('welcome');
});

Route::match(['get', 'post'], '/botman', 'BotManController@handle');
Route::get('/botman/tinker', 'BotManController@tinker');

Auth::routes();

Route::get('auth/telegram/callback', 'TelegramAuthController@handleTelegramCallback')->name('auth.telegram.handle');

Route::get('/home', 'HomeController@index')->name('home');

Route::resources([
    'users' => 'UsersController',
    'categories' => 'CategoryController',
    'companies' => 'CompanyController',
    'promotions' => 'PromotionController',
    'cashback' => 'CashbackHistoryController',
    'refferals' => 'RefferalsHistoryController',
    'payments' => 'RefferalsPaymentHistoryController',
]);

Route::get("/users/cashback/{id}","UsersController@cashBackPage")->name("usercashback.index");
Route::post("/users/cashback/add","UsersController@addCashBack")->name("usercashback.add");

Route::get('/image/{img}', function ($img) {

    $image = QrCode::format('png')
        ->size(300)->errorCorrection('H')
        ->generate("https://t.me/".env("APP_BOT_NAME")."?start=$img");

    return response($image)->header('Content-type','image/jpeg')->header("Cache-Control", "no-cache");
});

/*Route::get("/image/{img}",function ($img){

   return Storage::disk("public")->download($img);
});*/

