<?php

namespace App\Http\Controllers;

use App\Company;
use App\Enums\AchievementTriggers;
use App\Event;
use App\Events\AchievementEvent;
use App\InstaPromotion;
use App\Prize;
use App\Promotion;
use App\UplodedPhotos;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;

class InstaPromotionController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $instapromos = InstaPromotion::orderBy('position', 'DESC')
            ->paginate(15);

        $hasUploadPhotos = UplodedPhotos::where("activated", false)
                ->count() > 0;

        return view('admin.instapromos.index', compact('instapromos', 'hasUploadPhotos'))
            ->with('i', ($request->get('page', 1) - 1) * 15);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $companies = Company::all();

        return view('admin.instapromos.create', compact("companies"));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'photo_url' => 'max:1000',
            'promo_bonus' => 'required',
            'position' => 'required',
            'is_active' => 'required',
            'company_id' => 'required|integer',
        ]);

        $promotions = InstaPromotion::create([
            'title' => $request->get('title') ?? '',
            'description' => $request->get('description') ?? '',
            'photo_url' => $request->get('photo_url') ?? '',
            'promo_bonus' => $request->get('promo_bonus') ?? 0,
            'position' => $request->get('position') ?? 0,
            'is_active' => $request->get('is_active') == "on" ? true : false,
            'company_id' => $request->get('company_id') ?? null,

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('instapromos.index')
            ->with('success', 'Акция Instagram успешно добавлено');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\InstaPromotion $instaPromotion
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $promo = InstaPromotion::with(["company"])->find($id);

        return view('admin.instapromos.show', compact('promo'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\InstaPromotion $instaPromotion
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $promo = InstaPromotion::with(["company"])->find($id);

        $companies = Company::all();

        return view('admin.instapromos.edit', compact('promo', 'companies'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\InstaPromotion $instaPromotion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'photo_url' => 'max:1000',
            'promo_bonus' => 'required',
            'position' => 'required',
            'is_active' => 'required',
            'company_id' => 'required|integer',
        ]);


        $instapromo = InstaPromotion::find($id);
        $instapromo->title = $request->get("title");
        $instapromo->description = $request->get("description");
        $instapromo->photo_url = $request->get("photo_url");
        $instapromo->promo_bonus = $request->get("promo_bonus") ?? 0;
        $instapromo->position = $request->get("position") ?? 0;

        $instapromo->is_active = $request->get("is_active") == "on" ? true : false;
        $instapromo->company_id = $request->get("company_id") ?? null;


        $instapromo->save();

        return redirect()
            ->route('instapromos.index')
            ->with('success', 'Акция Instagram успешно отредактировано');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\InstaPromotion $instaPromotion
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $instapromo = InstaPromotion::find($id);
        $instapromo->delete();

        return redirect()
            ->route('instapromos.index')
            ->with('success', 'Акция Instagram успешно удалено');
    }

    public function channel($id)
    {
        $instapromo = InstaPromotion::find($id);


        $keyboard = [
            [
                ['text' => "\xF0\x9F\x91\x89Переход в бота", 'url' => "https://t.me/" . env("APP_BOT_NAME")],
            ],
        ];

        Telegram::sendPhoto([
            'chat_id' => "-1001392337757",
            'parse_mode' => 'Markdown',
            "photo" => InputFile::create($instapromo->photo_url),
            "caption" => "*" . $instapromo->title . "*\n_" . $instapromo->description . "_",
            'disable_notification' => 'true',
            'reply_markup' => json_encode([
                'inline_keyboard' =>
                    $keyboard
            ])
        ]);

        return redirect()
            ->route('instapromos.index')
            ->with('success', 'Акция Instagram успешно добавлено в канал');
    }

    public function duplication($id)
    {
        $instapromo = InstaPromotion::find($id);

        $instapromo = $instapromo->replicate();;
        $instapromo->save();

        return redirect()
            ->route('instapromos.index')
            ->with('success', 'Акция Instagram успешно продублирован');

    }

    public function uploadphotos(Request $request)
    {

        $uploadphotos = UplodedPhotos::with(["user"])
            ->where("activated", false)
            ->paginate(15);


        $instapromos = InstaPromotion::where("is_active", true)
            ->orderBy("position", "desc")
            ->get();


        return view('admin.instapromos.uploadphotos', compact('uploadphotos', 'instapromos'))
            ->with('i', ($request->get('page', 1) - 1) * 15);
    }

    public function accept(Request $request, $id)
    {
        $insta_promotions_id = $request->get("insta_promotions_id") ?? null;
        if (is_null($insta_promotions_id))
            return redirect()
                ->route('users.uploadphotos')
                ->with('success', 'Акция Instagram не найдена!');

        $photo = UplodedPhotos::find($id);

        $hasPromoActivated = UplodedPhotos::where("insta_promotions_id", $insta_promotions_id)
            ->where("user_id", $photo->user_id)
            ->first();

        if (!is_null($hasPromoActivated))
            return redirect()
                ->route('users.uploadphotos')
                ->with('success', 'Пользователь уже активировал данную акцию!');


        $photo->activated = true;
        $photo->insta_promotions_id = $insta_promotions_id;
        $photo->save();

        $bonus = (InstaPromotion::find($insta_promotions_id))->promo_bonus;
        $user = User::find($photo->user_id);
        $user->referral_bonus_count += $bonus;
        $user->save();

        event(new AchievementEvent(AchievementTriggers::MaxReferralBonusCount, $bonus, $user));

        Telegram::sendPhoto([
            'chat_id' => $user->telegram_chat_id,
            'parse_mode' => 'Markdown',
            "photo" => InputFile::create($photo->url),
            "caption" => "_Вам начислили бонус_ *$bonus ₽* _за участие в акции_",
        ]);

        return redirect()
            ->route('users.uploadphotos')
            ->with('success', 'Акция успешно подтверждена!');
    }

    public function decline($id)
    {
        $photo = UplodedPhotos::find($id);

        if (is_null($photo))
            return redirect()
                ->route('users.uploadphotos')
                ->with('success', 'Скриншот не найден!');

        $user = User::find($photo->user_id);

        if (is_null($user))
            return redirect()
                ->route('users.uploadphotos')
                ->with('success', 'Пользователь не найден!');

        Telegram::sendPhoto([
            'chat_id' => $user->telegram_chat_id,
            'parse_mode' => 'Markdown',
            "photo" => InputFile::create($photo->url),
            "caption" => "*Ваш скриншот отклонен!*",
        ]);

        $photo->delete();

        return redirect()
            ->route('users.uploadphotos')
            ->with('success', 'Скриншот к акции отклонен для пользователя!');
    }
}
