<?php

namespace App\Http\Controllers;

use App\Models\SkidkaServiceModels\RefferalsHistory;
use Illuminate\Http\Request;

class RefferalsHistoryController extends Controller
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
        $refferals = RefferalsHistory::orderBy('id', 'DESC')->paginate(15);

        return view('admin.refferals_histories.index', compact('refferals'))
            ->with('i', ($request->get('page', 1) - 1) * 15);
    }

}
