<?php

namespace App\Http\Controllers;

use App\Models\SkidkaServiceModels\Charity;
use App\Models\SkidkaServiceModels\CharityHistory;
use App\Models\SkidkaServiceModels\Company;
use App\Models\SkidkaServiceModels\InstaPromotion;
use App\Models\SkidkaServiceModels\UplodedPhoto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;

class CharityController extends Controller
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
        $charities = Charity::orderBy('position', 'DESC')
            ->paginate(15);

        return view('admin.charities.index', compact('charities'))
            ->with('i', ($request->get('page', 1) - 1) * 15);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.charities.create');
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
            'image_url' => 'required',
            'position' => 'required',
        ]);

        Charity::create([
            'title' => $request->get('title') ?? '',
            'description' => $request->get('description') ?? '',
            'image_url' => $request->get('image_url') ?? '',
            'position' => $request->get('position') ?? 0,
            'is_active' => $request->get('is_active') == "on" ? true : false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('charities.index')
            ->with('success', 'Благотворительная акция успешно добавлено');
    }

    /**
     * Display the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $charity = Charity::find($id);

        return view('admin.charities.show', compact('charity'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $charity = Charity::find($id);

        return view('admin.charities.edit', compact('charity'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image_url' => 'required',
            'position' => 'required',
        ]);


        $charity = Charity::find($id);
        $charity->title = $request->get("title");
        $charity->description = $request->get("description");
        $charity->image_url = $request->get("image_url");
        $charity->position = $request->get("position") ?? 0;
        $charity->is_active = $request->get("is_active") == "on" ? true : false;
        $charity->save();

        return redirect()
            ->route('charities.index')
            ->with('success', 'Благотворительная акция успешно отредактирована');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $charity = Charity::find($id);
        if ($charity->donates == 0) {
            $charity->delete();
            return redirect()
                ->route('charities.index')
                ->with('success', 'Вы успешно удалили благотворительную акцию');
        }

        $charity->is_active = 0;
        $charity->save();

        return redirect()
            ->route('charities.index')
            ->with('success', 'Вы успешно скрыли благотворительную акцию');
    }

    public function channel($id)
    {
        $charity = Charity::find($id);

        $keyboard = [
            [
                ['text' => "\xF0\x9F\x91\x89Переход в бота", 'url' => "https://t.me/" . env("APP_BOT_NAME")],
            ],
        ];

        Telegram::sendPhoto([
            'chat_id' => "-1001392337757",
            'parse_mode' => 'Markdown',
            "photo" => InputFile::create($charity->image_url),
            "caption" => "*" . $charity->title . "*\n_" . $charity->description . "_",
            'disable_notification' => 'true',
            'reply_markup' => json_encode([
                'inline_keyboard' =>
                    $keyboard
            ])
        ]);

        return redirect()
            ->route('charities.index')
            ->with('success', 'Благотворительная акция успешно добавлено в канал');
    }

    public function duplication($id)
    {
        $charity = Charity::find($id);

        $charity = $charity->replicate();
        $charity->save();

        $charity->title = $charity->title . "#" . $charity->id . "(copy)";
        $charity->save();

        return redirect()
            ->route('charities.index')
            ->with('success', 'Благотворительная акция успешно продублирован');

    }

    public function usersOn(Request $request,$id){
        $donates = CharityHistory::with(["user","company"])
            ->where("charity_id",$id)
            ->paginate(15);

        return view('admin.charities.userson', compact('donates'))
            ->with('i', ($request->get('page', 1) - 1) * 15);

    }

}
