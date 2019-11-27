<?php

namespace App\Http\Controllers;

use App\Promotion;
use App\User;
use Illuminate\Http\Request;

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

            $tmp_user = "".$request->get("user_id");
            $tmp_promo = "".$request->get("promotion_id");

            while(strlen($tmp_user)<10)
                $tmp_user.="0".$tmp_user;

            while(strlen($tmp_promo)<10)
                $tmp_promo.="0".$tmp_promo;

            $code = base64_encode("001".$tmp_user.$tmp_promo);

            $qrimage =  "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://t.me/".env("APP_BOT_NAME")."?start=$code";

            return view('home', compact('users','promotions','qrimage'));
        }

        return view('home', compact('users','promotions'));
    }




}
