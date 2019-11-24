<?php

namespace App\Http\Controllers;

use App\RefferalsHistory;
use Illuminate\Http\Request;

class RefferalsHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $refferals = RefferalsHistory::orderBy('id', 'DESC')->paginate(15);

        return view('admin.refferals_histories.index', compact('refferals'))
            ->with('i', ($request->input('page', 1) - 1) * 15);
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
     * @param  \App\RefferalsHistory  $refferalsHistory
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $ref = RefferalsHistory::find($id);

        return view('admin.refferals_histories.show', compact('ref'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RefferalsHistory  $refferalsHistory
     * @return \Illuminate\Http\Response
     */
    public function edit(RefferalsHistory $refferalsHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RefferalsHistory  $refferalsHistory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RefferalsHistory $refferalsHistory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RefferalsHistory  $refferalsHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy(RefferalsHistory $refferalsHistory)
    {
        //
    }
}
