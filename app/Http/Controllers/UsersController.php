<?php

namespace App\Http\Controllers;

use App\Models\SkidkaServiceModels\CashbackHistory;
use App\Models\SkidkaServiceModels\Company;
use App\User;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\Drivers\Telegram\TelegramDriver;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function statistic()
    {
        $count = User::count() ?? 0;

        $dayUsers = DB::table('users')
                ->whereDay('created_at', date('d'))
                ->count() ?? 0;
        return ["count" => $count, "day_users" => $dayUsers];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::with(["parent", "spentCashBack", "stats"])
            ->sortable(['id' => 'desc'])
            //->orderBy('id', 'DESC')
            ->paginate(15);

        $count = $this->statistic()["count"];

        $dayUsers = $this->statistic()["day_users"];

        return view('admin.users.index', compact('users', 'count', 'dayUsers'))
            ->with('i', ($request->get('page', 1) - 1) * 15);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        /*  $request->validate([
              'name'=> 'required',
              'email'=> 'required|email|unique:users',
              'password'=> 'required',
              'phone'=> 'required|unique:users',
              'avatar_url',
              'address'=> 'required',
              'sex'=> 'required',
              'age'=> 'required',
              'source'=> 'required',
              'telegram_chat_id'=> 'required',
              'referrals_count'=> 'required',
              'referral_bonus_count'=> 'required',
              'cashback_bonus_count'=> 'required',
              'is_admin'=> 'required',

          ]);

          $user = new User([
              'name' => $request->get('name'),
              'email' => $request->get('email'),
              'password' => Hash::make($request->get('password')),

              'fio_from_telegram' => $request->get('fio_from_telegram') ?? '',
              'fio_from_request' => $request->get('fio_from_request')?? '',
              'phone' => $request->get('phone')?? '',
              'avatar_url' => $request->get('avatar_url')?? '',
              'address' => $request->get('address')?? '',
              'sex' => $request->get('name')?? 1,
              'age' => $request->get('name')?? 18,
              'source' => $request->get('name')?? "001",
              'telegram_chat_id' => $request->get('name'),
              'referrals_count' => $request->get('name')?? 0,
              'referral_bonus_count' => $request->get('name')?? 0,
              'cashback_bonus_count' => $request->get('name')?? 0,
              'is_admin' => $request->get('name')?? false,
          ]);

          return back()->with('success', 'Пользователь успешно добавлен');*/
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //

        $user = User::with(["promos", "companies", "parent", "childs","cashbackinfos","cashbackinfos.company"])->find($id);

        return view('admin.users.show', compact('user'));
    }

    public function showByPhone($phone)
    {
        //

        $user = User::with(["promos", "companies"])->where("phone", $phone)->first();

        if ($user)
            return view('admin.users.show', compact('user'));
        return back()
            ->with("success", "Пользователь не найден!");
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $user = User::find($id);
        $companies = Company::all();
        return view('admin.users.edit', compact('user', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'is_admin' => 'required',
        ]);


        $user = User::find($id);
        $user->is_admin = $request->get("is_admin");

        $items = $request->get('company_ids');

        if ($items) {
            $cur_ids = array();
            if (count($user->companies) > 0) {
                foreach ($user->companies as $list) {
                    $cur_ids[] = $list->id;
                }

                $user->companies()->detach($cur_ids);
            }

            foreach ($items as $item) {
                $user->companies()->attach($item);
                $user->save();
            }
        }

        $user->save();

        return redirect()
            ->route('users.index')
            ->with('success', 'Пользователь успешно отредактирован');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $user = User::find($id);
        $user->delete();
        return redirect()
            ->route('users.index')
            ->with('success', 'Пользователь успешно удален');
    }

    public function cashBackPage($id)
    {

        $user = User::where("id", $id)->first();

        $companies = Company::all();

        return view('admin.users.cashback', compact('user', 'companies'));
    }

    public function addCashBack(Request $request)
    {

        if (auth()->user() != null) {
            $employee = User::find(auth()->user()->id);

            $money_in_check = $request->get("money_in_check");

            $user = User::find($request->get("id"));

            $bonus = $money_in_check * env("CAHSBAK_PROCENT") / 100;
            $user->cashback_bonus_count += $bonus;

            $user->updated_at = Carbon::now();

            $user->save();

            CashbackHistory::create([
                'money_in_check' => $money_in_check,
                'activated' => 1,
                'employee_id' => $employee->id,
            ]);

            Telegram::sendMessage([
                'chat_id' => $user->telegram_chat_id,
                'parse_mode' => 'Markdown',
                'text' => "Вам зачислен кэшбэк в размере $bonus",
            ]);

            return redirect()
                ->route('users.index')
                ->with('success', 'Кэшбэк успешно добавлен');
        }

        return redirect()
            ->route('users.index')
            ->with('error', 'Авторизируйтесь!');

    }

    public function search(Request $request)
    {
        if ($request->get("users-search") == null
        )
            return redirect()
                ->route('users.index')
                ->with('error', 'Нет данных!');


        try {


            $user = $request->get("users-search") ?? '';

            $users = User::with(["promos"])
                ->where("name", "like", "%$user%")
                ->orWhere("email", "like", "%$user%")
                ->orWhere("fio_from_telegram", "like", "%$user%")
                ->orWhere("fio_from_request", "like", "%$user%")
                ->orWhere("phone", "like", "%$user%")
                ->orWhere("address", "like", "%$user%")
                ->orderBy('id', "DESC")
                ->paginate(15);


        } catch (\Exception $e) {
            Log::info($e->getMessage() . " " . $e->getLine());
            $users = User::orderBy('id', 'DESC')->paginate(15);
        }

        $count = $this->statistic()["count"];

        $dayUsers = $this->statistic()["day_users"];

        return view('admin.users.index', compact('users', 'count', 'dayUsers'))
            ->with('i', ($request->get('page', 1) - 1) * 15);
    }


    public function getUserPromotions(Request $request, $id)
    {

        $promotions = (User::with(["promos"])->find($id))
            ->promos()
            ->orderBy('position', 'DESC')
            ->paginate(10);

        return view('admin.promotions.index_panel', compact('promotions'))
            ->with('i', ($request->get('page', 1) - 1) * 10);
    }

}
