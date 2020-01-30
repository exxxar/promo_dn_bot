<?php

namespace App\Http\Controllers;

use App\Company;
use App\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;

class CompanyController extends Controller
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
        $companies = Company::orderBy('position', 'DESC')
            ->paginate(15);

        return view('admin.companies.index', compact('companies'))
            ->with('i', ($request->get('page', 1) - 1) * 15);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $companies = Company::orderBy('position', 'DESC')
            ->paginate(15);
        //
        return view('admin.companies.create',compact("companies"));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'title' => 'required',
            'address' => 'required',
            'description' => 'required',
            'phone' => 'required',
            'bailee' => 'required',
            'logo_url' => 'required',
            'position' => 'required',
        ]);


        $company = Company::create([
            'title' => $request->get('title') ?? '',
            'address' => $request->get('address') ?? '',
            'description' => $request->get('description') ?? '',
            'phone' => $request->get('phone') ?? '',
            'email' => $request->get('email') ?? '',
            'bailee' => $request->get('bailee') ?? '',
            'logo_url' => $request->get('logo_url') ?? '',
            'position' => $request->get('position') ?? 0,
            'is_active' => $request->get('is_active') ?? false,
            'telegram_bot_url' => $request->get('telegram_bot_url') ?? '',
            'menu_url' => $request->get('menu_url') ?? null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('companies.index')
            ->with('success', 'Компания успешно добавлена');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Company $company
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $company = Company::find($id);

        return view('admin.companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company $company
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $company = Company::find($id);

        return view('admin.companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Company $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'address' => 'required',
            'description' => 'required',
            'phone' => 'required',
            'bailee' => 'required',
            'logo_url' => 'required',
            'position' => 'required',
        ]);


        $company = Company::find($id);
        $company->title = $request->get('title') ?? '';
        $company->address = $request->get('address') ?? '';
        $company->description = $request->get('description') ?? '';
        $company->phone = $request->get('phone') ?? '';
        $company->email = $request->get('email') ?? '';
        $company->bailee = $request->get('bailee') ?? '';
        $company->logo_url = $request->get('logo_url') ?? '';
        $company->position = $request->get('position') ?? 0;
        $company->is_active = $request->get('is_active') ?? false;
        $company->menu_url = $request->get('menu_url') ?? null;
        $company->telegram_bot_url = $request->get('telegram_bot_url') ?? '';
        $company->save();

        return redirect()
            ->route('companies.show', $id)
            ->with('success', 'Компания успешно отредактирована');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Company $company
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $company = Company::find($id);
        $company->delete();
        return redirect()
            ->route('companies.index')
            ->with('success', 'Компания успешно удалена');
    }

    public function channel($id){
        $company = Company::find($id);


        $keyboard = [
            [
                ['text' => "\xF0\x9F\x91\x89Переход в бота", 'url' =>"https://t.me/" . env("APP_BOT_NAME")],
            ],
        ];

        if (strlen(trim($company->telegram_bot_url)) > 0)
            array_push($tmp_keyboards, [
                ['text' => "\xF0\x9F\x91\x89Перейти в бота", 'url' => $company->telegram_bot_url],
            ]);


        Telegram::sendPhoto([
            'chat_id' => "-1001392337757",
            'parse_mode' => 'Markdown',
            "photo"=>InputFile::create($company->logo_url),
            "caption"=>"К нам присоединилось еще одно заведение!\n*".$company->title."*\n_".$company->description."_",
            'disable_notification' => 'true',
            'reply_markup' => json_encode([
                'inline_keyboard' =>
                    $keyboard
            ])
        ]);

        return redirect()
            ->route('companies.index')
            ->with('success', 'Компания успешно добавлена в канал');
    }

    public function hide(Request $request,$id){
        $company = Company::find($id);
        $company->is_active = !$company->is_active;
        $company->save();

        return redirect()
            ->route('companies.index')
            ->with('success', 'Статус компании изменен!');
    }
}
