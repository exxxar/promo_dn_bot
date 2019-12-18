<?php

namespace App\Http\Controllers;

use App\Achievement;
use App\Enums\AchievementTriggers;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AchievementsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

/*    public function index(){


    $title=urlencode('Заголовок вашей вкладки или веб-страницы');
    $url=urlencode('https://t.me/skidki_dn_bot?start=MDAxMDQ4NDY5ODcwMzAwMDAwMDAwMDA=');
    $summary=urlencode('Текстовое описание, которое вкратце рассказывает, зачем пользователям переходить по этой ссылке.');
    $image=urlencode('http://www.vash-web-site.ru/images/share-icon.jpg');


    return view("achievements",compact('url','title','summary','image'));
}*/

    public function index(Request $request)
    {
        //
        $achievements = Achievement::orderBy('id', 'DESC')
            ->orderBy('position', 'DESC')
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

        return view('admin.achievements.create',compact("triggers"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'=> 'required',
            'description'=> 'required',
            'ach_image_url'=> 'required',
            'trigger_type'=> 'required',
            'trigger_value'=> 'required',
            'prize_description'=> 'required',
            'prize_image_url'=> 'required',
            'position'=> 'required',
        ]);

        $achievement = Achievement::create([
            'title'=>$request->get('title')??'',
            'description'=> $request->get('description')??'',
            'ach_image_url'=> $request->get('ach_image_url')??'',
            'trigger_type'=> AchievementTriggers::getInstance(intval($request->get('trigger_type')))->value??'',
            'trigger_value'=> $request->get('trigger_value')??'',
            'prize_description'=> $request->get('prize_description')??'',
            'prize_image_url'=> $request->get('prize_image_url')??'',
            'position'=> $request->get('position')??0,
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
     * @param  \App\Event  $events
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $achievement = Achievement::find($id);

        $triggers = AchievementTriggers::getInstances();

        return view('admin.achievements.show', compact('achievement','triggers'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Event  $events
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $achievement = Achievement::find($id);


        $triggers = AchievementTriggers::getInstances();

        return view('admin.achievements.edit', compact('achievement','triggers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Event  $events
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title'=> 'required',
            'description'=> 'required',
            'ach_image_url'=> 'required',
            'trigger_type'=> 'required',
            'trigger_value'=> 'required',
            'prize_description'=> 'required',
            'prize_image_url'=> 'required',
            'position'=> 'required',

        ]);


        $achievement = Achievement::find($id);
        $achievement->title = $request->get("title")??'';
        $achievement->description = $request->get("description")??'';
        $achievement->ach_image_url = $request->get("ach_image_url")??'';
        $achievement->trigger_type = AchievementTriggers::getInstance(intval($request->get('trigger_type')))->value;
        $achievement->trigger_value = $request->get("trigger_value");
        $achievement->prize_description = $request->get("prize_description")??'';
        $achievement->prize_image_url = $request->get("prize_image_url")??'';
        $achievement->position = $request->get("position")??0;

        $achievement->save();

        return redirect()
            ->route('achievements.index')
            ->with('success', 'Достижение успешно отредактировано');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Event  $events
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $achievement = Achievement::find($id);
        $achievement->delete();
        return redirect()
            ->route('achievements.index')
            ->with('success', 'Достижение успешно удалено');
    }
}
