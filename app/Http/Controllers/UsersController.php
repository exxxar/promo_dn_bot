<?php

namespace App\Http\Controllers;

use App\CashbackHistory;
use App\User;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::orderBy('id', 'DESC')->paginate(15);

        return view('admin.users.index', compact('users'))
            ->with('i', ($request->input('page', 1) - 1) * 15);
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
     * @param  \Illuminate\Http\Request $request
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
              'name' => $request->input('name'),
              'email' => $request->input('email'),
              'password' => Hash::make($request->input('password')),

              'fio_from_telegram' => $request->input('fio_from_telegram') ?? '',
              'fio_from_request' => $request->input('fio_from_request')?? '',
              'phone' => $request->input('phone')?? '',
              'avatar_url' => $request->input('avatar_url')?? '',
              'address' => $request->input('address')?? '',
              'sex' => $request->input('name')?? 1,
              'age' => $request->input('name')?? 18,
              'source' => $request->input('name')?? "001",
              'telegram_chat_id' => $request->input('name'),
              'referrals_count' => $request->input('name')?? 0,
              'referral_bonus_count' => $request->input('name')?? 0,
              'cashback_bonus_count' => $request->input('name')?? 0,
              'is_admin' => $request->input('name')?? false,
          ]);

          return back()->with('success', 'Пользователь успешно добавлен');*/
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $user = User::with(["promos", "companies"])->find($id);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $user = User::find($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'is_admin' => 'required',
        ]);


        $user = User::find($id);
        $user->is_admin = $request->get("is_admin");
        $user->save();

        return back()->with('success', 'Пользователь успешно отредактирован');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $user = User::find($id);
        $user->delete();
        return back()->with('success', 'Пользователь успешно удален');
    }

    public function cashBackPage($id){

        $user = User::find($id);
        return view('admin.users.cashback', compact('user'));
    }

    public function addCashBack(Request $request){

        if (auth()->user()!=null) {
            $employee = User::find(auth()->user()->id);

            $money_in_check = $request->get("money_in_check");

            $user = User::find($request->get("id"));

            $bonus = $money_in_check * env("CAHSBAK_PROCENT") / 100;
            $user->cashback_bonus_count += $bonus;

            $user->save();

            CashbackHistory::create([
                'money_in_check' => $money_in_check,
                'activated' => 1,
                'employee_id' => $employee->id,
            ]);


            $botman = resolve('botman');
            $botman->say("Вам зачислен кэшбэк в размере $bonus", $user->telegram_chat_id, TelegramDriver::class);


            return back()->with('success', 'Кэшбэк успешно добавлен');
        }

        return back()->with('error', 'Авторизируйтесь!');

    }
}
