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

Route::get('/test_user',function (){
   $users = \App\User::with(["parent","childs"])->get();
   foreach($users as $user)
       foreach ($user->childs as $key=>$u) {
           echo ($key + 1) . ")" .$user->name." child=". $u->name . "<br>";
            foreach ($u->childs as $u1)
                echo $u1->name;
       }

});
Route::get('/ach','AchievementsController@index');
Route::get('/test_get_updates','BotManController@testGetUpdates');



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

    Route::post('/announce', 'HomeController@announce')
        ->name('users.announce');

    Route::post("/users/cashback/add","HomeController@cashback")
        ->name("users.cashback.add");


    Route::get("/users/show/byPhone/{phone}","UsersController@showByPhone")
        ->name("users.show.phone");


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
        'achievements' => 'AchievementsController',
    ]);

});






/*Route::get("/image/{img}",function ($img){

   return Storage::disk("public")->download($img);
});*/

