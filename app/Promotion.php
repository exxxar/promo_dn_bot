<?php

namespace App;

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


}
