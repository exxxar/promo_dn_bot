<?php

namespace App\Http\Controllers;

use App\Category;
use App\Company;
use App\Prize;
use App\Promotion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;

class PrizeController extends Controller
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
        $prizes = Prize::with(["company"])->orderBy('id', 'DESC')
            ->paginate(20);
        return view('admin.prizes.index', compact('prizes'))
            ->with('i', ($request->get('page', 1) - 1) * 20);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $companies = Company::all();

        return view("admin.prizes.create", compact('companies'));
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
            'company_id' => 'required',
            'summary_activation_count' => 'required',
            'is_active' => 'required',
        ]);
        $prize = Prize::create([
            'title' => $request->get('title') ?? '',
            'description' => $request->get('description') ?? '',
            'image_url' => $request->get('image_url') ?? '',
            'company_id' => $request->get('company_id') ?? '',

            'summary_activation_count' =>$request->get('summary_activation_count') ?? '',

            'is_active' =>$request->get('is_active') ?? '',


            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        return redirect()
            ->route('prizes.index')
            ->with('success', 'Приз успешно добавлен');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Prize $prize
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $prize = Prize::find($id);
        return view('admin.prizes.show', compact('prize'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Prize $prize
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $prize = Prize::find($id);
        return view('admin.prizes.edit', compact('prize'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Prize $prize
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image_url' => 'required',
            'company_id' => 'required',
            'summary_activation_count' => 'required',

            'is_active' => 'required',

        ]);
        $prize = Prize::find($id);
        $prize->title = $request->get("title");
        $prize->description = $request->get("description");
        $prize->image_url = $request->get("image_url");
        $prize->summary_activation_count = $request->get("summary_activation_count");
        $prize->is_active = $request->get("is_active");
        $prize->updated_at = Carbon::now();
        $prize->save();

        return redirect()
            ->route('prizes.index')
            ->with('success', 'Приз успешно отредактирован');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Prize $prize
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $prize = Prize::find($id);
        $prize->delete();
        return redirect()
            ->route('prizes.index')
            ->with('success', 'Приз успешно удален');
    }

    public function channel($id)
    {
        $prize = Prize::with(["company"])->find($id);

        $companyTitle = $prize->company->title;

        $keyboard = [
            [
                ['text' => "\xF0\x9F\x91\x89Переход в бота", 'url' => "https://t.me/" . env("APP_BOT_NAME")],
            ],
        ];

        Telegram::sendPhoto([
            'chat_id' => env("CHANNEL_ID"),
            'parse_mode' => 'Markdown',
            "photo" => InputFile::create($prize->image_url),
            "caption" => "Компания $companyTitle добавила новый приз в розыгрыш!\n*" . $prize->title . "*\n_" . $prize->description . "_",
            'disable_notification' => 'true',
            'reply_markup' => json_encode([
                'inline_keyboard' =>
                    $keyboard
            ])
        ]);

        return redirect()
            ->route('prizes.index')
            ->with('success', 'Приз успешно добавлен в канал');
    }

    public function duplication($id)
    {
        $prize = Prize::find($id);

        $prize = $prize->replicate();;
        $prize->save();

        return redirect()
            ->route('prizes.index')
            ->with('success', 'Приз успешно продублирован');

    }

}
