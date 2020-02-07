<?php

namespace App\Http\Controllers;

use App\CashbackHistory;
use App\Company;
use App\Promotion;
use App\User;
use Carbon\Carbon;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function instaURL()
    {
        if (!session_id()) {
            session_start();
        }

        $fb = new Facebook([
            'app_id' => env('FACEBOOK_APP_ID'),
            'app_secret' => env('FACEBOOK_APP_SECRET'),
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

        return $loginUrl;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::all();
        $promotions = Promotion::all();

        $json_data = file_get_contents(base_path('resources/lang/ru/messages.php'));
        $incoming_message = (json_decode($json_data, true))["menu_title_7"];

        $current_user = User::with(["companies"])->find(Auth::user()->id);

        $instaURL = $this->instaURL();

        if ($request->isMethod("POST")) {

            try {
                $tmp_user = "" . (User::where("phone", "=", $request->get("user_phone_gen"))->first())->telegram_chat_id;
                $tmp_promo = "" . $request->get("promotion_id");

                while (strlen($tmp_user) < 10)
                    $tmp_user = "0" . $tmp_user;

                while (strlen($tmp_promo) < 10)
                    $tmp_promo = "0" . $tmp_promo;

                $code = base64_encode("001" . $tmp_user . $tmp_promo);

                $qrimage = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://t.me/" . env("APP_BOT_NAME") . "?start=$code";

                return view('home', compact('users', 'promotions', 'qrimage', 'current_user', "tmp_user", "tmp_promo", 'incoming_message', 'instaURL'));
            } catch (\Exception $e) {
                return redirect()
                    ->back();
            }
        }

        return view('home', compact('users', 'promotions', 'current_user', 'incoming_message', 'instaURL'));
    }

    public function searchAjax(Request $request)
    {

        $vowels = array("(", ")", "-", " ");
        $tmp_phone = $request->get("query");

        $tmp_phone = str_replace($vowels, "", $tmp_phone);


        return User::where('phone', 'like', '%' . $tmp_phone . '%')->get();


    }

    public function search(Request $request)
    {
        $vowels = array("(", ")", "-", " ");
        $tmp_phone = $request->get("phone");

        $tmp_phone = str_replace($vowels, "", $tmp_phone);


        $user = User::where("phone", $tmp_phone)->first();

        if ($user)
            return redirect()
                ->route("users.show", $user->id);

        return back()
            ->with("success", "Пользователь не найден!");

    }

    public function cashback(Request $request)
    {


        $vowels = array("(", ")", "-", " ");
        $tmp_phone = $request->get("user_phone");
        $check_info = $request->get("check_info");
        $money_in_check = $request->get("money_in_check");
        $company_id = $request->get("company_id");

        $tmp_phone = str_replace($vowels, "", $tmp_phone);

        if ($request->has("id"))
            $user = User::where("id", $request->get("id"))->first();
        else
            $user = User::where("phone", $tmp_phone)->first();

        if ($user) {
            $cashBack = round(intval($money_in_check) * env("CAHSBAK_PROCENT") / 100);
            $user->cashback_bonus_count += $cashBack;
            $user->updated_at = Carbon::now();
            $user->save();


            CashbackHistory::create([
                'money_in_check' => $money_in_check,
                'activated' => 1,
                'employee_id' => Auth::user()->id,
                'company_id' => $company_id,
                'check_info' => $check_info,
                'user_phone' => $tmp_phone,
                'user_id' => $user->id,

            ]);
            try {

                Telegram::sendMessage([
                    'chat_id' => $user->telegram_chat_id,
                    'parse_mode' => 'Markdown',
                    'text' => "Сумма в чеке *$money_in_check* руб.\nВам начислен *CashBack* в размере *$cashBack* руб.",
                    'disable_notification' => 'false'
                ]);
            } catch (\Exception $e) {
                $user->activated = false;
                $user->save();

                return back()
                    ->with("success", "Пользователь больше не использует данный телеграм-бот!");
            }
            return back()
                ->with("success", "Кэшбэк успешно добавлен!");
        }

        CashbackHistory::create([
            'money_in_check' => $money_in_check,
            'activated' => 0,
            'employee_id' => Auth::user()->id,
            'company_id' => $company_id,
            'check_info' => $check_info,
            'user_phone' => $tmp_phone,

        ]);

        return back()
            ->with("success", "Пользователь не найден!Кэшбэк добавлен на номер!");

    }


    public function announce(Request $request)
    {

        $users = User::all();

        $announce_title = $request->get("announce_title") ?? '';
        $announce_url = $request->get("announce_url") ?? null;
        $announce_message = $request->get("announce_message") ?? '';
        $send_to_type = $request->get("send_to_type") ?? 0;

        if (trim($announce_title) == '' || trim($announce_message) == '')
            return back()
                ->with("success", "Заголовок или сообщение не заполнены!");

        if ($send_to_type == 3) {

            $keyboard = [
                [
                    ['text' => "\xF0\x9F\x91\x89Переход в бота", 'url' => "https://t.me/" . env("APP_BOT_NAME")],
                ],
            ];

            Telegram::sendPhoto([
                'chat_id' => env("CHANNEL_ID"),
                'parse_mode' => 'HTML',
                "photo" => InputFile::create($announce_url),
                'disable_notification' => 'true',

            ]);

            Telegram::sendMessage([
                'chat_id' => env("CHANNEL_ID"),
                'parse_mode' => 'HTML',
                "text" => "<b>" . $announce_title . "</b>\n <em>" . $announce_message . "</em>",
                'disable_notification' => 'true',
                'reply_markup' => json_encode([
                    'inline_keyboard' =>
                        $keyboard
                ])
            ]);

            return;
        }

        foreach ($users as $user) {

            if (!is_numeric($user->telegram_chat_id) && strlen("$user->telegram_chat_id") >= 10)
                continue;

            $doSend = ($send_to_type == 0) ||
                ($send_to_type == 1 && $user->activated == 1) ||
                ($send_to_type == 2 && $user->activated == 0);

            try {

                if ($doSend) {

                    Telegram::sendMessage([
                        'chat_id' => $user->telegram_chat_id,
                        'parse_mode' => 'Markdown',
                        'text' => "*$announce_title*\n_ $announce_message _",
                        'disable_notification' => 'false'
                    ]);

                    if ($announce_url != null)
                        Telegram::sendPhoto([
                            'chat_id' => $user->telegram_chat_id,
                            'photo' => InputFile::create($announce_url)
                        ]);
                }
            } catch (\Exception $e) {

            }
        }

        return back()->with("success", "Сообщения успешно отправлены!");
    }

    public function sender(Request $request)
    {
        return view("sender");
    }


    public function announceCustom(Request $request)
    {

        $pattern = "/([0-9]{9})/";

        $send_to = $request->get("send_to");
        $announce_title = $request->get("announce_title");
        $announce_url = $request->get("announce_url");
        $announce_message = $request->get("announce_message");

        preg_match_all($pattern, $send_to, $matches);
        ini_set('max_execution_time', 1000000);

        foreach ($matches[0] as $m) {
            try {
                Log::info($m);
                Telegram::sendMessage([
                    'chat_id' => "$m",
                    'parse_mode' => 'Markdown',
                    'text' => "*$announce_title*\n_ $announce_message _ \n $announce_url",
                    'disable_notification' => 'false'
                ]);
            } catch (\Exception $e) {
                Log::info($e);
            }
        }

        ini_set('max_execution_time', 60);

        return redirect()
            ->back();
    }

    public function cabinet()
    {
        $title = urlencode('Заголовок вашей вкладки или веб-страницы');
        $url = urlencode('https://t.me/skidki_dn_bot?start=MDAxMDQ4NDY5ODcwMzAwMDAwMDAwMDA=');
        $summary = urlencode('Текстовое описание, которое вкратце рассказывает, зачем пользователям переходить по этой ссылке.');
        $image = urlencode('http://www.vash-web-site.ru/images/share-icon.jpg');


        return view("cabinet", compact('url', 'title', 'summary', 'image'));
    }

    public function content(Request $request)
    {
        $jsonString = file_get_contents(base_path('resources/lang/ru/messages.php'));
        $params = json_decode($jsonString);

        return view("admin.langs.index", compact('params'));
    }

    public function translations()
    {
        return view('admin.langs.index');
    }


    public function instagramCallabck()
    {
        if (!session_id()) {
            session_start();
        }
        $fb = new Facebook([
            'app_id' => env('FACEBOOK_APP_ID'), // Replace {app-id} with your app id
            'app_secret' => env('FACEBOOK_APP_SECRET'),
            'default_graph_version' => 'v3.2',
            'persistent_data_handler' => 'session'
        ]);

        $helper = $fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch (FacebookResponseException $e) {
            // When Graph returns an error
            Log::error('Graph returned an error: ' . $e->getMessage());
            return;
        } catch (FacebookSDKException $e) {
            Log::error('Facebook SDK returned an error: ' . $e->getMessage());
            return;
        }

        if (!isset($accessToken)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');

                $error = sprintf("Error:%s\nError Code:%s\nError Reason:%s\nError Description:%s",
                    $helper->getError(),
                    $helper->getErrorCode(),
                    $helper->getErrorReason(),
                    $helper->getErrorDescription()
                );

                Log::info($error);
            } else {
                header('HTTP/1.0 400 Bad Request');
                Log::error('Bad request');
            }
            return;
        }

        Log::info("Access Token = ".$accessToken->getValue());
        $oAuth2Client = $fb->getOAuth2Client();
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);

        Log::info(print_r($tokenMetadata,true));
        $tokenMetadata->validateAppId(env('FACEBOOK_APP_ID')); // Replace {app-id} with your app id
        // If you know the user ID this access token belongs to, you can validate it here
        //$tokenMetadata->validateUserId('123');
        $tokenMetadata->validateExpiration();

        if (!$accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (FacebookSDKException $e) {
                Log::error("Error getting long-lived access token: " . $e->getMessage() );
                return;
            }

            Log::info("Long-lived=".$accessToken->getValue());
        }

        $_SESSION['fb_access_token'] = (string)$accessToken;

        ////ig_hashtag_search?user_id=17841407882850175&q=альпинадонну - находим хэштег
        ////17913566134265893/top_media?user_id=17841407882850175&fields=caption,media_type,media_url
        ////17913566134265893/top_media?user_id=17841407882850175
        ////17913566134265893/recent_media?fields=caption,media_type,media_url,like_count,id,permalink&user_id=17841407882850175 - находим недавние медиа объекты с хэштегом
        //
        ////17841407882850175?fields=mentioned_media.media_id(18040865266238470){caption,media_type,username}   - получаем инфу о пользователе по идентификации медиа-объекта
        //

       //
        //$requestUserPhotos = $fb->request('GET', '/17841407882850175?fields=mentioned_media.media_id(18040865266238470){caption,media_type,username}');

        $accounts = $fb->request('GET', '/me/accounts');

        $responses = $fb->sendBatchRequest([
            'data' => $accounts,
        ], $accessToken);

        foreach ($responses as $key => $response) {
          $dataId = json_decode($response->getBody(),true)["id"];

            $req = $fb->request('GET', "/$dataId?fields=instagram_business_account");

            $responses = $fb->sendBatchRequest([
                'data' => $req,
            ], $accessToken);


            try {
                $localId = json_decode($responses[0]->getBody(), true)["instagram_business_account"]["id"];
                Log::info("ID=$localId");
            }catch (\Exception $e){

            }


        }
    }
}
