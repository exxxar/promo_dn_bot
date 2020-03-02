<?php

namespace App\Http\Controllers;

use App\Models\SkidkaServiceModels\GeoPosition;
use App\Models\SkidkaServiceModels\GeoQuest;
use App\Models\SkidkaServiceModels\Promotion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;

class GeoPositionController extends Controller
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
        $geo_positions = GeoPosition::orderBy('position', 'DESC')
            ->paginate(15);



        return view('admin.geo_positions.index', compact('geo_positions')
            ->with('i', ($request->get('page', 1) - 1) * 15);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $promotions = Promotion::all();

        return view('admin.geo_positions.create', compact('promotions'));
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
            'latitude' => 'required',
            'longitude' => 'required',
            'radius' => 'required',


        ]);

        GeoQuest::create([
            'title' => $request->get('title') ?? '',
            'description' => $request->get('description') ?? '',
            'image_url' => $request->get('image_url') ?? '',
            'latitude' => $request->get('latitude') ?? 0,
            'longitude' => $request->get('longitude') ?? 0,
            'radius' => $request->get('radius') ?? 0,
            'local_promotion_id' => $request->get('local_promotion_id') ?? null,
            'local_reward' => $request->get('local_reward') ?? 0,
            'in_time_range' => $request->get('in_time_range') == "on",
            'range_time_value' => $request->get('range_time_value') ?? 0,
            'time_end' => $request->get('time_end') ?? '22:00',
            'time_start' => $request->get('time_start') ?? '10:00',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('geo_positions.index')
            ->with('success', 'Позиция успешно добавлен');
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
        $position = GeoPosition::find($id);

        return view('admin.geo_positions.show', compact('position'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Article $article
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $geo_position = GeoPosition::find($id);

        $promotions = Promotion::all();


        return view('admin.geo_positions.edit', compact('geo_position','promotions'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image_url' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'radius' => 'required',
        ]);

        $position = GeoPosition::find($id);
        $position->title = $request->get("title") ?? '';
        $position->description = $request->get("description") ?? '';
        $position->image_url = $request->get("image_url") ?? '';
        $position->latitude = $request->get("latitude") ?? 0;
        $position->longitude = $request->get("longitude") ?? 0;
        $position->radius = $request->get("radius") ?? 0;

        $position->local_promotion_id = $request->get('local_promotion_id') ?? null;
        $position->local_reward = $request->get('local_reward') ?? 0;
        $position->in_time_range = $request->get('in_time_range') == "on";
        $position->range_time_value = $request->get('range_time_value') ?? 0;
        $position->time_end = $request->get('time_end') ?? '22:00';
        $position->time_start = $request->get('time_start') ?? '10:00';
        $position->created_at = Carbon::now();
        $position->updated_at = Carbon::now();

        $position->save();

        return redirect()
            ->route('geo_positions.index')
            ->with('success', 'Позиция успешно отредактирована');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Article $article
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $position = GeoPosition::find($id);
        $position->delete();

        return redirect()
            ->route('geo_positions.index')
            ->with('success', 'Позиция успешно удалена');
    }

    public function copy($id)
    {

        $position = GeoPosition::find($id);

        $position = $position->replicate();
        $position->title = "[Копия]" . $position->title;
        $position->save();

        return redirect()
            ->route('geo_positions.index')
            ->with('success', 'Позиция успешно продублирована');

    }

    public function channel($id)
    {
        $position = GeoPosition::find($id);

        Telegram::sendPhoto([
            'chat_id' => "-1001392337757",
            'parse_mode' => 'Markdown',
            "photo" => InputFile::create($position->image_url),
            'disable_notification' => 'true'
        ]);

        return redirect()
            ->route('geo_positions.index')
            ->with('success', 'Новая позиция успещно добавлена в канал');
    }
}
