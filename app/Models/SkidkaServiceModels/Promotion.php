<?php

namespace App\Models\SkidkaServiceModels;

use App\User;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    //
    protected $dates = ['start_at','end_at'];

    protected $fillable = [
        'title',
        'description',
        'promo_image_url',
        'start_at',
        'end_at',
        'activation_count',
        'current_activation_count',
        'location_address',
        'location_coords',
        'activation_text',
        'immediately_activate',
        'refferal_bonus',
        'company_id',
        'category_id',
        'created_at',
        'updated_at',
        'handler',
        'position',
        'user_can_activate_count'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_has_promos', 'promotion_id', 'user_id')
            ->withTimestamps()
            ->withPivot('user_activation_count');
    }

    public function getPromoUrl(){
        $skidobotik = User::where("email","skidobot@gmail.com")->first();
        $tmp_user_id = (string)$skidobotik->telegram_chat_id;
        $tmp_promo_id = (string)$this->id;

        while (strlen($tmp_promo_id) < 10)
            $tmp_promo_id = "0" . $tmp_promo_id;
        $code = base64_encode("001" . $tmp_user_id . $tmp_promo_id);

        return  "https://t.me/" . env("APP_BOT_NAME") . "?start=$code";

    }

    public function category()
    {
        return $this->belongsTo(Category::class, "category_id", "id");
    }

    public function company()
    {
        return $this->belongsTo(Company::class, "company_id", "id");
    }

    public function onPromo($chatId)
    {
        $on_promo = $this->users()->where('telegram_chat_id', "$chatId")->first();
        return ($on_promo != null)&&$on_promo->pivot->user_activation_count==0;
    }

    public function isActive()
    {
        $time_0 = (date_timestamp_get(new DateTime($this->start_at)));
        $time_1 = (date_timestamp_get(new DateTime($this->end_at)));
        $time_2 = date_timestamp_get(now());
        return $time_2 >= $time_0 && $time_2 < $time_1;
    }
}
