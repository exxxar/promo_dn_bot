<?php

namespace App\Http\Controllers;

use App\Models\SkidkaServiceModels\Company;
use App\Models\SkidkaServiceModels\Event;
use App\Models\SkidkaServiceModels\Promotion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;

class EventsController extends Controller
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
        $events = Event::orderBy('position', 'DESC')
            ->paginate(15);

        return view('admin.events.index', compact('events'))
            ->with('i', ($request->get('page', 1) - 1) * 15);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $companies = Company::all();

        $promotions = Promotion::orderBy('id', 'DESC')
            ->get();

        return view('admin.events.create', compact("companies", "promotions"));
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
            'event_image_url' => 'max:1000',
            'start_at' => 'required',
            'end_at' => 'required',
            'company_id' => 'required|integer',
            'position' => 'required',
        ]);

        $promotions = Event::create([
            'title' => $request->get('title') ?? '',
            'description' => $request->get('description') ?? '',
            'event_image_url' => $request->get('event_image_url') ?? '',
            'start_at' => \Carbon\Carbon::parse($request->get('start_at') ?? Carbon::now()),
            'end_at' => \Carbon\Carbon::parse($request->get('end_at') ?? Carbon::now()),

            'company_id' => $request->get('company_id'),
            'promo_id' => $request->get('promo_id') ?? null,
            'category_id' => $request->get('category_id'),
            'position' => $request->get('position') ?? 0,
            'need_qr' => $request->get('need_qr') == "on" ? 1 : 0,

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('events.index')
            ->with('success', 'Мероприятие успешно добавлено');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Event $events
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event = Event::with(["company", "promotion"])->find($id);

        return view('admin.events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Event $events
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $event = Event::with(["promotion", "company"])->find($id);

        $companies = Company::all();

        $promotions = Promotion::orderBy('id', 'DESC')
            ->get();

        return view('admin.events.edit', compact('event', 'companies', 'promotions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Event $events
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'event_image_url' => 'max:1000',
            'start_at' => 'required',
            'end_at' => 'required',
            'position' => 'required',
            'company_id' => 'required|integer',

        ]);


        $promotion = Event::find($id);
        $promotion->title = $request->get("title");
        $promotion->description = $request->get("description");
        $promotion->event_image_url = $request->get("event_image_url");
        $promotion->start_at = \Carbon\Carbon::parse($request->get('start_at') ?? Carbon::now());
        $promotion->end_at = \Carbon\Carbon::parse($request->get('end_at') ?? Carbon::now());
        $promotion->position = $request->get("position") ?? 0;

        $promotion->company_id = $request->get("company_id");

        $promotion->promo_id = $request->get("promo_id") ?? null;

        $promotion->need_qr = $request->get("need_qr") == "on" ? 1 : 0;

        $promotion->save();

        return redirect()
            ->route('events.index')
            ->with('success', 'Мероприятие успешно отредактировано');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Event $events
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $event = Event::find($id);
        $event->delete();
        return redirect()
            ->route('events.index')
            ->with('success', 'Мероприятие успешно удалено');
    }

    public function channel($id)
    {
        $event = Event::find($id);


        $keyboard = [
            [
                ['text' => "\xF0\x9F\x91\x89Переход в бота", 'url' => "https://t.me/" . env("APP_BOT_NAME")],
            ],
        ];

        Telegram::sendPhoto([
            'chat_id' => "-1001392337757",
            'parse_mode' => 'Markdown',
            "photo" => InputFile::create($event->event_image_url),
            "caption" => "*" . $event->title . "*\n_" . $event->description . "_",
            'disable_notification' => 'true',
            'reply_markup' => json_encode([
                'inline_keyboard' =>
                    $keyboard
            ])
        ]);

        return redirect()
            ->route('events.index')
            ->with('success', 'Мероприятие успешно добавлено в канал');
    }
}
