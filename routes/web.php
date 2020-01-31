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
use Vinkla\Instagram\Instagram;

Route::get('/test_admin', function () {
   return view("newadmin");
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

Route::get("/insta_test", function (Request $request) {
    //$instagram = new Instagram(env("INSTAGRAM_TOKEN"));


    // dd($instagram->self());
    if ($request->has("code")) {
        Log::info($request->get("code"));
        $code = $request->get("code") ?? null;

        try {

            $query = "client_id=160196558729808&client_secret=fbcbf311468e7464ac3b521284265810&grant_type=authorization_code&redirect_uri=https://skidka-service.ru/insta&code=$code";
            $context = stream_context_create(array(
                'http' => array(
                    'method' => 'POST',
                    'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
                    'content' => $query
                ),
            ));

            ini_set('max_execution_time', 1000000);
            $content = file_get_contents(
                $file = 'https://api.instagram.com/oauth/access_token',
                $use_include_path = false,
                $context);
            ini_set('max_execution_time', 60);

            $content = json_decode($content);
            Log::info($content->access_token);
            dd($content);

        } catch (ErrorException $e) {
            $content = [];
            Log::info($e->getMessage() . " " . $e->getLine());
        }
    }


});

Route::get("/fbtest_start", function () {
    if (!session_id()) {
        session_start();
    }

    $fb = new Facebook\Facebook([
        'app_id' => '489970145264352', // Replace {app-id} with your app id
        'app_secret' => 'c841f98459b2fd386a03ed9c0ac63b04',
        'default_graph_version' => 'v3.2',
        'persistent_data_handler' => 'session'
    ]);

    $helper = $fb->getRedirectLoginHelper();


    $permissions = ['manage_pages',
        'pages_show_list',
        'publish_pages',
        'business_management',
        'instagram_basic',
        'public_profile',
        'instagram_manage_insights',
        'instagram_manage_comments',
        'ads_management'];
    $loginUrl = $helper->getLoginUrl('https://skidka-service.ru/insta', $permissions);

    echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';
});

Route::get("/insta", function (Request $request) {
    if (!session_id()) {
        session_start();
    }
    $fb = new Facebook\Facebook([
        'app_id' => '489970145264352', // Replace {app-id} with your app id
        'app_secret' => 'c841f98459b2fd386a03ed9c0ac63b04',
        'default_graph_version' => 'v3.2',
        'persistent_data_handler' => 'session'
    ]);

    $helper = $fb->getRedirectLoginHelper();

    try {
        $accessToken = $helper->getAccessToken();
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }

    if (!isset($accessToken)) {
        if ($helper->getError()) {
            header('HTTP/1.0 401 Unauthorized');
            echo "Error: " . $helper->getError() . "\n";
            echo "Error Code: " . $helper->getErrorCode() . "\n";
            echo "Error Reason: " . $helper->getErrorReason() . "\n";
            echo "Error Description: " . $helper->getErrorDescription() . "\n";
        } else {
            header('HTTP/1.0 400 Bad Request');
            echo 'Bad request';
        }
        exit;
    }

// Logged in
    echo '<h3>Access Token</h3>';
    var_dump($accessToken->getValue());

// The OAuth 2.0 client handler helps us manage access tokens
    $oAuth2Client = $fb->getOAuth2Client();

// Get the access token metadata from /debug_token
    $tokenMetadata = $oAuth2Client->debugToken($accessToken);
    echo '<h3>Metadata</h3>';
    var_dump($tokenMetadata);

// Validation (these will throw FacebookSDKException's when they fail)
    $tokenMetadata->validateAppId('489970145264352'); // Replace {app-id} with your app id
// If you know the user ID this access token belongs to, you can validate it here
//$tokenMetadata->validateUserId('123');
    $tokenMetadata->validateExpiration();

    if (!$accessToken->isLongLived()) {
        // Exchanges a short-lived access token for a long-lived one
        try {
            $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
            exit;
        }

        echo '<h3>Long-lived</h3>';
        var_dump($accessToken->getValue());
    }

    $_SESSION['fb_access_token'] = (string)$accessToken;

    ////ig_hashtag_search?user_id=17841407882850175&q=альпинадонну - находим хэштег
    ////17913566134265893/top_media?user_id=17841407882850175&fields=caption,media_type,media_url
    ////17913566134265893/top_media?user_id=17841407882850175
    ////17913566134265893/recent_media?fields=caption,media_type,media_url,like_count,id,permalink&user_id=17841407882850175 - находим недавние медиа объекты с хэштегом
    //
    ////17841407882850175?fields=mentioned_media.media_id(18040865266238470){caption,media_type,username}   - получаем инфу о пользователе по идентификации медиа-объекта
    //


    $requestUserPhotos = $fb->request('GET', '/17841407882850175?fields=mentioned_media.media_id(18040865266238470){caption,media_type,username}');

    $batch = [
        'user-photos' => $requestUserPhotos,
    ];

    echo '<h1>Make a batch request</h1>' . "\n\n";

    try {
        $responses = $fb->sendBatchRequest($batch,$accessToken);
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }

    foreach ($responses as $key => $response) {
        if ($response->isError()) {
            $e = $response->getThrownException();
            echo '<p>Error! Facebook SDK Said: ' . $e->getMessage() . "\n\n";
            echo '<p>Graph Said: ' . "\n\n";
            var_dump($e->getResponse());
        } else {
            echo "<p>(" . $key . ") HTTP status code: " . $response->getHttpStatusCode() . "<br />\n";
            echo "Response: " . $response->getBody() . "</p>\n\n";
            echo "<hr />\n\n";
        }
    }

});

Route::get('/', function (Request $request) {
    $companies = \App\Company::with(["promotions", "promotions.category"])->where("is_active", true)->get();
    $article = \App\Article::where("part", \App\Enums\Parts::Terms_of_use)->first() ?? null;
    $url = $article == null ? env("APP_URL") : ($article)->url;
    return view('welcome', compact("companies", 'url'));
})->name("welcome");

Route::post('/send-request', function (Request $request) {

    $name = $request->get('name') ?? "Не указано";
    $phone = $request->get('phone') ?? "Не указано";
    $message = $request->get('message') ?? "Не указано";
    $agree = $request->get('agree') ?? false;

    Log::info("Имя:$name\nТелефон:$phone\nСообщение:$message");

    $user = \App\User::where("phone", $phone)->first();
    if (!is_null($user)) {
        Telegram::sendMessage([
            'chat_id' => $user->telegram_chat_id,
            'parse_mode' => 'Markdown',
            'text' => "_Ваше сообщение получено! Спасибо за то что помогаете нам быть лучше!_",
            'disable_notification' => 'true'
        ]);
    }

    return redirect()->route("welcome");
})->name("send.callback");


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

    Route::get("/promotions/copy/{id}", "PromotionController@copy")->name("promotions.copy");
    Route::get("/promotions/channel/{id}", "PromotionController@channel")->name("promotions.channel");
    Route::get("/events/channel/{id}", "EventsController@channel")->name("events.channel");
    Route::get("/companies/channel/{id}", "CompanyController@channel")->name("companies.channel");
    Route::get("/companies/hide/{id}", "CompanyController@hide")->name("companies.hide");
    Route::get("/achievements/channel/{id}", "AchievementsController@channel")->name("achievements.channel");
    Route::get("/prizes/channel/{id}", "PrizeController@channel")->name("prizes.channel");
    Route::get("/promocodes/change_status/{id}", "PromocodeController@change_status")->name("promocodes.changestatus");

    Route::get("/duplication/channel/{id}", "PrizeController@duplication")->name("prizes.duplication");

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

