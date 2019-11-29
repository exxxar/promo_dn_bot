<?php

namespace App\Http\Controllers;

use App\CashbackHistory;
use App\Promotion;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        if ($request->isMethod("POST")) {

            $tmp_user = "" . $request->get("user_id");
            $tmp_promo = "" . $request->get("promotion_id");

            while (strlen($tmp_user) < 10)
                $tmp_user .= "0" . $tmp_user;

            while (strlen($tmp_promo) < 10)
                $tmp_promo .= "0" . $tmp_promo;

            $code = base64_encode("001" . $tmp_user . $tmp_promo);

            $qrimage = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://t.me/" . env("APP_BOT_NAME") . "?start=$code";

            return view('home', compact('users', 'promotions', 'qrimage'));
        }

        return view('home', compact('users', 'promotions'));
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

        $tmp_phone = str_replace($vowels, "", $tmp_phone);

        if ($request->has("id"))
            $user = User::where("id", $request->get("id"))->first();
        else
            $user = User::where("phone", $tmp_phone)->first();

        if ($user) {
            $user->cashback_bonus_count += round(intval($money_in_check) * env("CAHSBAK_PROCENT") / 100);
            $user->save();

            CashbackHistory::create([
                'money_in_check' => $money_in_check,
                'activated' => 1,
                'employee_id' => Auth::user()->id,
                'check_info' => $check_info,
                'user_phone' => $tmp_phone,

            ]);
            return back()
                ->with("success", "Кэшбэк успешно добавлен!");
        }

        CashbackHistory::create([
            'money_in_check' => $money_in_check,
            'activated' => 0,
            'employee_id' => Auth::user()->id,
            'check_info' => $check_info,
            'user_phone' => $tmp_phone,

        ]);

        return back()
            ->with("success", "Пользователь не найден!Кэшбэк добавлен на номер!");

    }


}
