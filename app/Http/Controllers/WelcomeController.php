<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class WelcomeController extends Controller
{
    //
    public function index(Request $request)
    {
        $companies = \App\Company::with(["promotions", "promotions.category"])
            ->where("is_active", true)
            ->get();

        $terms = \App\Article::where("part", \App\Enums\Parts::Terms_of_use)->first() ?? null;
        $terms = $terms == null ? env("APP_URL") : ($terms)->url;

        $faq = \App\Article::where("part", \App\Enums\Parts::How_to_use)->first() ?? null;
        $faq = $faq == null ? env("APP_URL") : ($faq)->url;

        $suppliers = \App\Article::where("part", \App\Enums\Parts::Suppliers)->first() ?? null;
        $suppliers = $suppliers == null ? env("APP_URL") : ($suppliers)->url;

        return view('welcome', compact("companies", 'terms', 'faq', 'suppliers'));
    }

    public function sendRequestFromSite(Request $request)
    {
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
    }

    public function terms()
    {
        return view("terms");
    }

    public function policy()
    {
        return view("policy");
    }
}
