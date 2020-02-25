<?php

namespace App\Http\Controllers;

use App\Category;
use App\Company;
use App\GeoQuest;
use App\Promotion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;

class GeoQuestController extends Controller
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
        //
        $geo_quests = GeoQuest::orderBy('position', 'DESC')
            ->paginate(15);

        return view('admin.geo_quests.index', compact('geo_quests'))
            ->with('i', ($request->get('page', 1) - 1) * 15);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('admin.geo_quests.create');
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
            'start_at' => 'required',
            'end_at' => 'required',
        ]);

        GeoQuest::create([
            'title' => $request->get('title') ?? '',
            'description' => $request->get('description') ?? '',
            'image_url' => $request->get('image_url') ?? '',
            'is_active' => $request->get('is_active')??false,
            'promotion_id' => $request->get('promotion_id') ?? null,
            'reward_bonus' => $request->get('reward_bonus') ?? 0,
            'position' => $request->get('position') ?? 0,
            'start_at' => $request->get('start_at')??Carbon::now(),
            'end_at' => $request->get('end_at')??Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('geo_quests.index')
            ->with('success', 'Квест успешно добавлен');
    }

    /**
     * Display the specified resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $quest = GeoQuest::find($id);

        return view('admin.geo_quests.show', compact('quest'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Article $article
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $geo_quests = GeoQuest::find($id);


        return view('admin.geo_quests.edit', compact('geo_quests'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Article $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'start_at' => 'required',
            'end_at' => 'required',
        ]);

        $quest = GeoQuest::find($id);
        $quest->title = $request->get("title") ?? '';
        $quest->description = $request->get("description") ?? '';
        $quest->image_url = $request->get("image_url") ?? '';
        $quest->is_active = $request->get("is_active") ?? false;
        $quest->reward_bonus = $request->get("reward_bonus") ?? '';
        $quest->position = $request->get("position") ?? 0;
        $quest->start_at = $request->get("start_at") ?? Carbon::now();
        $quest->end_at = $request->get("end_at") ?? Carbon::now();

        $quest->is_visible = $request->get('is_visible') ?? 0;
        $quest->position = $request->get('position') ?? 0;
        $quest->save();

        return redirect()
            ->route('geo_quests.index')
            ->with('success', 'Квест отредактирован');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Article $article
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $quest = GeoQuest::find($id);
        $quest->delete();

        return redirect()
            ->route('geo_quests.index')
            ->with('success', 'Квест успешно удален');
    }

    public function copy($id){

        $quest = GeoQuest::find($id);

        $quest = $quest->replicate();
        $quest->title = "[Копия]".$quest->title;
        $quest->save();

        return redirect()
            ->route('geo_quests.index')
            ->with('success', 'Квест успешно продублирован');

    }

    public function channel($id){
        $quest = GeoQuest::find($id);

        Telegram::sendPhoto([
            'chat_id' => "-1001392337757",
            'parse_mode' => 'Markdown',
            "photo"=>InputFile::create($quest->image_url),
            'disable_notification' => 'true'
        ]);

        return redirect()
            ->route('promotions.index')
            ->with('success', 'Квест успешно добавлен в канал');
    }

}
