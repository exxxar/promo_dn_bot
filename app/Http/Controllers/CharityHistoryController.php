<?php

namespace App\Http\Controllers;

use App\CharityHistory;
use Illuminate\Http\Request;

class CharityHistoryController extends Controller
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
        $charityhistories = CharityHistory::with(["user", "company", "charity"])
            ->orderBy('id', 'DESC')
            ->paginate(15);

        return view('admin.charityhistories.index', compact('charityhistories'))
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\CharityHistory $charityHistory
     * @return \Illuminate\Http\Response
     */
    public function show(CharityHistory $charityHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\CharityHistory $charityHistory
     * @return \Illuminate\Http\Response
     */
    public function edit(CharityHistory $charityHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\CharityHistory $charityHistory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CharityHistory $charityHistory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\CharityHistory $charityHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy(CharityHistory $charityHistory)
    {
        //
    }
}
