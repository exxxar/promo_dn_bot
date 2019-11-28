<?php

namespace App\Http\Controllers;

use App\Company;
use App\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        $events = Event::orderBy('id', 'DESC')->paginate(15);

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

        return view('admin.events.create',compact("companies"));
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
            'event_image_url'=> 'max:1000',
            'start_at'=> 'required',
            'end_at'=> 'required',
            'company_id'=> 'required|integer',
        ]);

        $promotions = Event::create([
            'title'=>$request->get('title')??'',
            'description'=> $request->get('description')??'',
            'event_image_url'=> $request->get('event_image_url')??'',
            'start_at'=> $request->get('start_at')??'',
            'end_at'=> $request->get('end_at')??'',

            'company_id'=> $request->get('company_id'),
            'category_id'=> $request->get('category_id'),

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
     * @param  \App\Event  $events
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event = Event::with(["company"])->find($id);

        return view('admin.events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Event  $events
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $event = Event::find($id);

        $companies = Company::all();

        return view('admin.events.edit', compact('event','companies'));
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
            'event_image_url'=> 'max:1000',
            'start_at'=> 'required',
            'end_at'=> 'required',
            'company_id'=> 'required|integer',

        ]);


        $promotion = Event::find($id);
        $promotion->title = $request->get("phone");
        $promotion->description = $request->get("description");
        $promotion->event_image_url = $request->get("event_image_url");
        $promotion->start_at = $request->get("start_at");
        $promotion->end_at = $request->get("end_at");

        $promotion->company_id = $request->get("company_id");

        $promotion->save();

        return redirect()
            ->route('events.index')
            ->with('success', 'Мероприятие успешно отредактировано');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Event  $events
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
}
