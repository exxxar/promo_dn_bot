<?php

namespace App\Models\SkidkaServiceModels;

use App\User;
use Illuminate\Database\Eloquent\Model;

class UplodedPhoto extends Model
{
    protected $fillable = [
        'url',
        'activated',
        'user_id',
        'insta_promotions_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function insta()
    {
        return $this->belongsTo(InstaPromotion::class, "insta_promotions_id", "id");
    }
}
