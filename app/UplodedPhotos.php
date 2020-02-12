<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UplodedPhotos extends Model
{
    protected $fillable = [
        'url',
        'activated',
        'user_id',
        'insta_promotions_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', "user_id", "id");
    }

    public function insta()
    {
        return $this->belongsTo('App\InstaPromotion', "insta_promotions_id", "id");
    }
}
