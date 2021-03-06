<?php

namespace App\Http\Controllers;

use App\Enums\AchievementTriggers;
use App\Models\SkidkaServiceModels\Achievement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;

class AchievementsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Request $request)
    {
        //
        $achievements = Achievement::where("is_active", 1)
            ->orderBy('position', 'ASC')
            ->paginate(15);


        return view('admin.achievements.index', compact('achievements'))
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
        $triggers = AchievementTriggers::getInstances();

        return view('admin.achievements.create', compact("triggers"));
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
            'ach_image_url' => 'required',
            'trigger_type' => 'required',
            'trigger_value' => 'required',
            'prize_description' => 'required',
            'prize_image_url' => 'required',
            // 'position' => 'required',
        ]);

        $achievement = Achievement::create([
            'title' => $request->get('title') ?? '',
            'description' => $request->get('description') ?? '',
            'ach_image_url' => $request->get('ach_image_url') ?? '',
            'trigger_type' => AchievementTriggers::getInstance(intval($request->get('trigger_type')))->value ?? '',
            'trigger_value' => $request->get('trigger_value') ?? '',
            'prize_description' => $request->get('prize_description') ?? '',
            'prize_image_url' => $request->get('prize_image_url') ?? '',
            'position' => $request->get('position') ?? 0,
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('achievements.index')
            ->with('success', 'Достижение успешно добавлено');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Event $events
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $achievement = Achievement::find($id);

        $triggers = AchievementTriggers::getInstances();

        return view('admin.achievements.show', compact('achievement', 'triggers'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Event $events
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $achievement = Achievement::find($id);


        $triggers = AchievementTriggers::getInstances();

        return view('admin.achievements.edit', compact('achievement', 'triggers'));
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
            'ach_image_url' => 'required',
            'trigger_type' => 'required',
            'trigger_value' => 'required',
            'prize_description' => 'required',
            'prize_image_url' => 'required',
            //'position' => 'required',

        ]);


        $achievement = Achievement::find($id);
        $achievement->title = $request->get("title") ?? '';
        $achievement->description = $request->get("description") ?? '';
        $achievement->ach_image_url = $request->get("ach_image_url") ?? '';
        $achievement->trigger_type = AchievementTriggers::getInstance(intval($request->get('trigger_type')))->value;
        $achievement->trigger_value = $request->get("trigger_value");
        $achievement->prize_description = $request->get("prize_description") ?? '';
        $achievement->prize_image_url = $request->get("prize_image_url") ?? '';
        $achievement->position = $request->get("position") ?? 0;
        $achievement->is_active = $request->get("is_active") ? true : false;

        $achievement->save();

        return redirect()
            ->route('achievements.index')
            ->with('success', 'Достижение успешно отредактировано');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Event $events
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $achievement = Achievement::find($id);
        $achievement->is_active = false;
        $achievement->save();

        //$achievement->delete();
        return redirect()
            ->route('achievements.index')
            ->with('success', 'Достижение успешно удалено');
    }

    public function channel($id)
    {
        $achievement = Achievement::find($id);


        $keyboard = [
            [
                ['text' => "\xF0\x9F\x91\x89Переход в бота", 'url' => "https://t.me/" . env("APP_BOT_NAME")],
            ],
        ];

        Telegram::sendPhoto([
            'chat_id' => "-1001392337757",
            'parse_mode' => 'Markdown',
            "photo" => InputFile::create($achievement->ach_image_url),
            "caption" => "Новое достижение уже доступно для вас! Дерзайте!\n*" . $achievement->title . "*\n_" . $achievement->description . "_",
            'disable_notification' => 'true',
            'reply_markup' => json_encode([
                'inline_keyboard' =>
                    $keyboard
            ])
        ]);

        return redirect()
            ->route('events.index')
            ->with('success', 'Достижение успешно добавлено в канал');
    }
}
