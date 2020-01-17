<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    //
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
        'position'
    ];

    public function users()
    {
        return $this->belongsToMany('App\User', 'user_has_promos', 'promotion_id', 'user_id')
            ->withTimestamps();
    }



    public function category()
    {
        return $this->belongsTo('App\Category',"category_id","id");
    }

    public function company()
    {
        return $this->belongsTo('App\Company',"company_id","id");
    }

    public function isNotActiveByUser($chatId)
    {
        $on_promo = $this->users()->where('telegram_chat_id', "$chatId")->first();

        $time_0 = (date_timestamp_get(new DateTime($this->start_at)));
        $time_1 = (date_timestamp_get(new DateTime($this->end_at)));
        $time_2 = date_timestamp_get(now());

        return ($on_promo == null && $time_2 >= $time_0 && $time_2 < $time_1);
    }

}
