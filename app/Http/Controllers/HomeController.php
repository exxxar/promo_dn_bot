<?php

namespace App\Http\Controllers;

use App\CashbackHistory;
use App\Promotion;
use App\User;
use Carbon\Carbon;
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::all();
        $promotions = Promotion::all();

        $current_user = User::with(["companies"])->find(Auth::user()->id);

        if ($request->isMethod("POST")) {

            $tmp_user = "" . $request->get("user_id");
            $tmp_promo = "" . $request->get("promotion_id");

            while (strlen($tmp_user) < 10)
                $tmp_user .= "0" . $tmp_user;

            while (strlen($tmp_promo) < 10)
                $tmp_promo .= "0" . $tmp_promo;

            $code = base64_encode("001" . $tmp_user . $tmp_promo);

            $qrimage = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://t.me/" . env("APP_BOT_NAME") . "?start=$code";

            return view('home', compact('users', 'promotions', 'qrimage', 'current_user'));
        }

        return view('home', compact('users', 'promotions', 'current_user'));
    }

    public function searchAjax(Request $request)
    {

        $vowels = array("(", ")", "-", " ");
        $tmp_phone = $request->get("query");

        $tmp_phone = str_replace($vowels, "", $tmp_phone);


        return  User::where('phone', 'like', '%' . $tmp_phone . '%')->get();


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

            Telegram::sendMessage([
                'chat_id' => $user->telegram_chat_id,
                'parse_mode' => 'Markdown',
                'text' => "Сумма в чеке *$money_in_check* руб.\nВам начислен *CashBack* в размере *$cashBack* руб.",
                'disable_notification' => 'false'
            ]);

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
}
