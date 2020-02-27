<?php

namespace App\Http\Controllers;

use App\Models\SkidkaServiceModels\CharityHistory;
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


}
