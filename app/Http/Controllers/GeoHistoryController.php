<?php

namespace App\Http\Controllers;

use App\Models\SkidkaServiceModels\GeoHistory;
use App\Models\SkidkaServiceModels\GeoPosition;
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

}
