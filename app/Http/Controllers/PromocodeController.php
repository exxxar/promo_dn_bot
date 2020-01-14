<?php

namespace App\Http\Controllers;

use App\Company;
use App\Promocode;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PromocodeController extends Controller
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
        $promocodes = Promocode::with(["user","company"])->orderBy('id', 'DESC')
            ->paginate(15);

        $companies = Company::all();

        return view('admin.promocodes.index', compact('promocodes','companies'))
            ->with('i', ($request->get('page', 1) - 1) * 15);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $companies = Company::all();

        return view("admin.promocodes.create",compact("companies"));
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
            'code'=> 'required|unique:promocodes',
        ]);

        $promocode = Promocode::create([
            'code'=>$request->get('code')??'',
            'company_id'=>$request->get('company_id')??'',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        return redirect()
            ->route('promocodes.index')
            ->with('success', 'Промокод успешно добавлен');
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Promocode  $promocodes
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $promocode = Promocode::find($id);
        return view('admin.promocodes.show', compact('promocode'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Promocode  $promocodes
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $promocode = Promocode::find($id);
        return view('admin.promocodes.edit', compact('promocode'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Promocode  $promocodes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'code'=> 'required',
            'prize_has_taken'=> 'required',
            'company_id'=> 'required',
        ]);
        $promocode = Promocode::find($id);
        $promocode->code = $request->get("code");
        $promocode->prize_has_taken = $request->get("prize_has_taken");
        $promocode->company_id = $request->get("company_id");
        $promocode->updated_at = Carbon::now();

        $promocode->save();
        return redirect()
            ->route('promocodes.index')
            ->with('success', 'Промокод успешно отредактирован');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Promocode  $promocodes
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $promocode = Promocode::find($id);
        $promocode->delete();
        return redirect()
            ->route('promocodes.index')
            ->with('success', 'Промокод успешно удален');
    }
}
