<?php

namespace App\Http\Controllers;

use App\Models\SkidkaServiceModels\CashbackHistory;
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
        $cashbacks = CashbackHistory::with(["employee","company","user"])
            ->orderBy('id', 'DESC')
            ->paginate(15);

        return view('admin.cashback_histories.index', compact('cashbacks'))
            ->with('i', ($request->get('page', 1) - 1) * 15);
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

}
