<?php

namespace App\Http\Controllers;

use App\RefferalsPaymentHistory;
use Illuminate\Http\Request;

class RefferalsPaymentHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $payments = RefferalsPaymentHistory::orderBy('id', 'DESC')->paginate(15);

        return view('admin.refferals_payment_histories.index', compact('payments'))
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
     * @param  \App\RefferalsPaymentHistory  $refferalsPaymentHistory
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //


        $ref = RefferalsPaymentHistory::find($id);

        return view('admin.referals_payment_histories.show', compact('ref'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RefferalsPaymentHistory  $refferalsPaymentHistory
     * @return \Illuminate\Http\Response
     */
    public function edit(RefferalsPaymentHistory $refferalsPaymentHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RefferalsPaymentHistory  $refferalsPaymentHistory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RefferalsPaymentHistory $refferalsPaymentHistory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RefferalsPaymentHistory  $refferalsPaymentHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy(RefferalsPaymentHistory $refferalsPaymentHistory)
    {
        //
    }
}
