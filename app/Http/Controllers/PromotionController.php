<?php

namespace App\Http\Controllers;

use App\Category;
use App\Company;
use App\Promotion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $promotions = Promotion::orderBy('id', 'DESC')->paginate(15);

        return view('admin.promotions.index', compact('promotions'))
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
        $companies = Company::all();
        $categories = Category::all();

        return view('admin.promotions.create',compact("companies","categories"));
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
            'title'=> 'required',
            'description'=> 'required',
            'promo_image_url'=> 'max:1000',
            'start_at'=> 'required',
            'end_at'=> 'required',
            'activation_count'=> 'required',
            'location_address'=> 'required',
            'company_id'=> 'required|integer',
            'category_id'=> 'required|integer',
            'refferal_bonus'=> 'integer',
        ]);

        $promotions = Promotion::create([
            'title'=>$request->get('title')??'',
            'description'=> $request->get('description')??'',
            'promo_image_url'=> $request->get('promo_image_url')??'',
            'start_at'=> $request->get('start_at')??'',
            'end_at'=> $request->get('end_at')??'',
            'activation_count'=> $request->get('activation_count')??'',
            'location_address'=> $request->get('location_address')??'',
            'company_id'=> $request->get('company_id'),
            'category_id'=> $request->get('category_id'),
            'current_activation_count'=>0,
            'location_coords'=> $request->get('location_coords')??'',
            'immediately_activate'=>$request->get('immediately_activate')??false,
            'refferal_bonus'=>$request->get('refferal_bonus')??0,
            'activation_text'=>$request->get('activation_text')??'',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('promotions.index')
            ->with('success', 'Акция успешно добавлена');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Promotion  $promotion
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $promotion = Promotion::with(["company","category"])->find($id);



        return view('admin.promotions.show', compact('promotion'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Promotion  $promotion
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $promotion = Promotion::find($id);

        $companies = Company::all();
        $categories = Category::all();

        return view('admin.promotions.edit', compact('promotion','categories','companies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Promotion  $promotion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        //
        $request->validate([
            'title'=> 'required',
            'description'=> 'required',
            'promo_image_url'=> 'max:1000',
            'start_at'=> 'required',
            'end_at'=> 'required',
            'activation_count'=> 'required',
            'location_address'=> 'required',
            'company_id'=> 'required|integer',
            'category_id'=> 'required|integer',
            'refferal_bonus'=> 'integer',
        ]);


        $promotion = Promotion::find($id);
        $promotion->title = $request->get("title");
        $promotion->description = $request->get("description");
        $promotion->promo_image_url = $request->get("promo_image_url");
        $promotion->start_at = $request->get("start_at");
        $promotion->end_at = $request->get("end_at");
        $promotion->immediately_activate = $request->get("immediately_activate")??false;
        $promotion->activation_count = $request->get("activation_count");
        $promotion->location_address = $request->get("location_address");
        $promotion->location_coords = $request->get("location_coords");
        $promotion->company_id = $request->get("company_id");
        $promotion->category_id = $request->get("category_id");
        $promotion->refferal_bonus = $request->get("refferal_bonus");
        $promotion->save();

        return redirect()
            ->route('promotions.index')
            ->with('success', 'Акция успешно отредактирована');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Promotion  $promotion
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $promotion = Promotion::find($id);
        $promotion->delete();
        return redirect()
            ->route('promotions.index')
            ->with('success', 'Акция успешно удалена');
    }
}
