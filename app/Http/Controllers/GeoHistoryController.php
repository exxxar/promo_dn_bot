<?php

namespace App\Http\Controllers;

use App\GeoHistory;
use App\GeoPosition;
use Illuminate\Http\Request;

class GeoHistoryController extends Controller
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
        $geo_histories = GeoHistory::paginate(15);

        return view('admin.geo_histories.index', compact('geo_histories'))
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
     * @param  \App\GeoHistory  $geoHistory
     * @return \Illuminate\Http\Response
     */
    public function show(GeoHistory $geoHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\GeoHistory  $geoHistory
     * @return \Illuminate\Http\Response
     */
    public function edit(GeoHistory $geoHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\GeoHistory  $geoHistory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, GeoHistory $geoHistory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\GeoHistory  $geoHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy(GeoHistory $geoHistory)
    {
        //
    }
}
