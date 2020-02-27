<?php

namespace App\Models\SkidkaServiceModels;

use App\User;
use Illuminate\Database\Eloquent\Model;

class GeoHistory extends Model
{

    protected $fillable = [
        'geo_quest_id',
        'geo_position_id',
        'user_id'
    ];

    public function geoPosition()
    {
        return $this->hasOne(GeoPosition::class,'id','geo_position_id');
    }

    public function geoQuest()
    {
        return $this->hasOne(GeoQuest::class,'id','geo_quest_id');
    }

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }
}
