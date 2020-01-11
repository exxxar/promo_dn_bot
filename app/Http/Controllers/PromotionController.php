<?php

namespace App\Http\Controllers;

use App\Category;
use App\Company;
use App\Promotion;
use App\User;
use BotMan\BotMan\Messages\Attachments\Image;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;

class PromotionController extends Controller
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
        $promotions = Promotion::orderBy('position', 'DESC')
            ->paginate(15);

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
            'activation_text'=> 'required',
            'location_address'=> 'required',
            'company_id'=> 'required|integer',
            'category_id'=> 'required|integer',
            'refferal_bonus'=> 'integer',
            'position'=> 'required',
        ]);

        $promotions = Promotion::create([
            'title'=>$request->get('title')??'',
            'description'=> $request->get('description')??'',
            'promo_image_url'=> $request->get('promo_image_url')??'',
            'start_at'=> $request->get('start_at')??'',
            'end_at'=> $request->get('end_at')??'',
            'activation_count'=> $request->get('activation_count')??'',
            'activation_text'=> $request->get('activation_text')??'',
            'location_address'=> $request->get('location_address')??'',
            'company_id'=> $request->get('company_id'),
            'category_id'=> $request->get('category_id'),
            'current_activation_count'=>0,
            'handler'=> $request->get('handler')??null,
            'location_coords'=> $request->get('location_coords')??'',
            'immediately_activate'=>$request->get('immediately_activate')??false,
            'refferal_bonus'=>$request->get('refferal_bonus')??0,
            'position'=>$request->get('position')??0,
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
            'activation_text'=> 'required',
            'location_address'=> 'required',
            'position'=> 'required',
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
        $promotion->handler = $request->get("handler")??null;
        $promotion->activation_count = $request->get("activation_count");
        $promotion->location_address = $request->get("location_address");
        $promotion->location_coords = $request->get("location_coords");
        $promotion->activation_text = $request->get("activation_text");
        $promotion->company_id = $request->get("company_id");
        $promotion->category_id = $request->get("category_id");
        $promotion->refferal_bonus = $request->get("refferal_bonus");
        $promotion->position = $request->get("position")??0;
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

    public function copy($id){

        $promotion = Promotion::find($id);

        $promotion = $promotion->replicate();
        $promotion->title = "[Копия]".$promotion->title;
        $promotion->save();

        $companies = Company::all();
        $categories = Category::all();

        return view('admin.promotions.edit', compact('promotion','categories','companies'));

    }

    public function channel($id){
        $promotion = Promotion::find($id);

        Telegram::sendPhoto([
            'chat_id' => "-1001392337757",
            'parse_mode' => 'Markdown',
            "photo"=>InputFile::create($promotion->promo_image_url),
            'disable_notification' => 'true'
        ]);

        $user = User::where("telegram_chat_id",env("DEVELOPER_ID"));

        $tmp_id = $user->telegram_chat_id;

        while (strlen($tmp_id) < 10)
            $tmp_id = "0" . $tmp_id;

        $tmp_promo_id = $promotion->id;
        while (strlen($tmp_promo_id) < 10)
            $tmp_promo_id = "0" . $tmp_promo_id;

        $code = base64_encode("003" . $tmp_id . $tmp_promo_id);

        $keyboard = [
            [
                ['text' => "\xF0\x9F\x91\x89Детальнее", 'url' =>"https://t.me/" . env("APP_BOT_NAME") . "?start=$code"],
            ],
        ];

        Telegram::sendMessage([
            'chat_id' => "-1001392337757",
            'parse_mode' => 'Markdown',
            "text"=>"*".$promotion->title."*\n_".$promotion->description."_",
            'disable_notification' => 'true',
            'reply_markup' => json_encode([
                'inline_keyboard' =>
                    $keyboard
            ])
        ]);

        return redirect()
            ->route('promotions.index')
            ->with('success', 'Акция успешно добавлена в канал');
    }
}
