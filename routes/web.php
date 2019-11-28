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




Route::prefix('admin')->group(function () {

    Route::get('/', 'HomeController@index')
        ->name('home');

    Route::post('/', 'HomeController@index')
        ->name('home.qr');


    Route::post('/search', 'UsersController@search')
        ->name('users.search');

    Route::post('/announce', 'UsersController@announce')
        ->name('users.announce');

    Route::post("/users/cashback/add","UsersController@addCashBack")
        ->name("users.cashback.add");

    Route::get("/users/cashback/{id}","UsersController@cashBackPage")->name("users.cashback.index");

    Route::resources([
        'users' => 'UsersController',
        'categories' => 'CategoryController',
        'companies' => 'CompanyController',
        'promotions' => 'PromotionController',
        'cashback' => 'CashbackHistoryController',
        'refferals' => 'RefferalsHistoryController',
        'payments' => 'RefferalsPaymentHistoryController',
        'events' => 'EventsController',
    ]);

});






/*Route::get("/image/{img}",function ($img){

   return Storage::disk("public")->download($img);
});*/

