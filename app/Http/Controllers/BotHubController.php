<?php

namespace App\Http\Controllers;

use App\BotHub;
use App\Classes\TestApiBot;
use App\Enums\Parts;
use App\Models\SkidkaServiceModels\Article;
use App\Models\SkidkaServiceModels\Company;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use ReflectionMethod;

class BotHubController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['apiMethods']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $bots = BotHub::orderBy('id', 'DESC')
            ->paginate(15);


        return view('admin.bot_hubs.index', compact('bots'))
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

        return view('admin.bot_hubs.create', compact("companies"));
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
            'token_prod' => 'required',
            'token_dev' => 'required',
            'bot_pic' => 'required',
            'bot_url' => 'required|unique:bot_hubs',
            'description' => 'required',
            'money' => 'required|numeric',
            'money_per_day' => 'required|numeric',
            'company_id' => 'required|numeric',
            'webhook_url' => 'required',
        ]);

        $bot = BotHub::create([
            'token_prod' => $request->get('token_prod') ?? '',
            'token_dev' => $request->get('token_dev') ?? '',
            'bot_pic' => $request->get('bot_pic') ?? '',
            'bot_url' => $request->get('bot_url') ?? '',
            'description' => $request->get('description') ?? '',
            'is_active' => $request->get('is_active') == "on",
            'money' => $request->get('money') ?? 0,
            'money_per_day' => $request->get('money_per_day') ?? 0,

            'company_id' => $request->get('company_id') ?? null,

            'webhook_url' => $request->get('webhook_url') ?? '',

            'created_at' => Carbon::now("+3:00"),
            'updated_at' => Carbon::now("+3:00"),
        ]);

        return redirect()
            ->route('bot_hubs.index')
            ->with('success', 'Бот успешно добавлен');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Article $article
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $bot = BotHub::find($id);

        return view('admin.bot_hubs.show', compact('bot'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Article $article
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $bot = BotHub::find($id);

        $companies = Company::all();

        return view('admin.bot_hubs.edit', compact('bot', 'companies'));

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
            'token_prod' => 'required',
            'token_dev' => 'required',
            'bot_pic' => 'required',
            'bot_url' => 'required|unique:bot_hubs',
            'description' => 'required',
            'money' => 'required|numeric',
            'money_per_day' => 'required|numeric',
            'company_id' => 'required|numeric',
            'webhook_url' => 'required',
        ]);

        $bot = BotHub::find($id);
        $bot->token_prod = $request->get("token_prod") ?? '';
        $bot->token_dev = $request->get("token_dev") ?? '';
        $bot->bot_pic = $request->get("bot_pic") ?? '';
        $bot->bot_url = $request->get("bot_url") ?? '';
        $bot->description = $request->get("description") ?? '';
        $bot->is_active = $request->get("is_active") == "on";
        $bot->money = $request->get("money") ?? 0;
        $bot->money_per_day = $request->get("money_per_day") ?? 0;
        $bot->company_id = $request->get("company_id") ?? null;
        $bot->webhook_url = $request->get("webhook_url") ?? '';

        $bot->save();

        return redirect()
            ->route('bot_hubs.index')
            ->with('success', 'Бот успешно отредактирован');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Article $article
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $bot = BotHub::find($id);
        $bot->delete();

        return redirect()
            ->route('bot_hubs.index')
            ->with('success', 'Бот успешно удален');
    }

    public function setWebHook($id)
    {
        $bot = BotHub::find($id);

        try {

            $postdata = http_build_query(
                array(
                    'url' => $bot->webhook_url,
                )
            );

            $opts = array('http' =>
                array(
                    'method' => 'POST',
                    'header' => 'Content-Type: application/x-www-form-urlencoded',
                    'content' => $postdata
                )
            );

            $context = stream_context_create($opts);

            $result = file_get_contents("https://api.telegram.org/bot" . (config("app.debug") ? $bot->token_dev : $bot->token_prod) . "/setWebhook", false, $context);

        }catch (\Exception $e){
            return redirect()
                ->route('bot_hubs.index')
                ->with('success', 'Ошибка установки WebHook');
        }

        return redirect()
            ->route('bot_hubs.index')
            ->with('success', 'WebHook успешно установлен');
    }

    public function unsetWebHook($id)
    {
        $bot = BotHub::find($id);

        try {

            $postdata = http_build_query(
                array(
                    'url' => ''
                )
            );

            $opts = array('http' =>
                array(
                    'method' => 'POST',
                    'header' => 'Content-Type: application/x-www-form-urlencoded',
                    'content' => $postdata
                )
            );

            $context = stream_context_create($opts);

            $result = file_get_contents("https://api.telegram.org/bot" . (config("app.debug") ? $bot->token_dev : $bot->token_prod) . "/setWebhook", false, $context);

        }catch (\Exception $e){
            return redirect()
                ->route('bot_hubs.index')
                ->with('success', 'Ошибка уаления WebHook');
        }

        return redirect()
            ->route('bot_hubs.index')
            ->with('success', 'WebHook успешно удален');
    }
    public function apiMethods(Request $request)
    {
        $chatId = $request->get("chatId");
        $botName = $request->get("bot_name");
        $query = $request->get("query");

        $objects = [
            [
                "class" => TestApiBot::class,
                "object" => new TestApiBot($botName, $chatId),
                "methods" => config("bot_api_routes.test_api_bot")
            ]
        ];

        $find = false;
        foreach ($objects as $object) {
            $matches = [];
            $arguments = [];
            foreach ($object["methods"] as $key => $method) {

                if (preg_match("$key$/i", $query, $matches) != false) {
                    foreach ($matches as $match)
                        array_push($arguments, $match);

                    try {
                        $reflectionMethod = new ReflectionMethod($object["class"], $method);
                        $reflectionMethod->invokeArgs($object["object"], $arguments);

                        $find = true;
                    } catch (\ReflectionException $e) {
                        Log::error($e->getMessage()." ".$e->getLine());
                    }

                    break;
                }
            }
        }

        if (!$find)
            return response()
                ->json([
                    "message" => "path not found",
                    "status" => 404
                ]);

        return response()
            ->json([
                "message" => "success",
                "status" => 200
            ]);
    }
}
