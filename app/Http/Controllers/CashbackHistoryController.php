<?php

namespace App\Http\Controllers;

use App\CashbackHistory;
use Illuminate\Http\Request;

class CashbackHistoryController extends Controller
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
        $cashbacks = CashbackHistory::with(["employee"])
            ->orderBy('id', 'DESC')
            ->paginate(15);

        return view('admin.cashback_histories.index', compact('cashbacks'))
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
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CashbackHistory  $cashbackHistory
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $cash = CashbackHistory::find($id);

        return view('admin.cashback_histories.show', compact('cash'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CashbackHistory  $cashbackHistory
     * @return \Illuminate\Http\Response
     */
    public function edit(CashbackHistory $cashbackHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CashbackHistory  $cashbackHistory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CashbackHistory $cashbackHistory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CashbackHistory  $cashbackHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy(CashbackHistory $cashbackHistory)
    {
        //
    }
}
