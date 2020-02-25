<?php

namespace App;

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
        return $this->hasOne('App\GeoPosition','id','geo_position_id');
    }

    public function geoQuest()
    {
        return $this->hasOne('App\GeoQuest','id','geo_quest_id');
    }

    public function user()
    {
        return $this->hasOne('App\User','id','user_id');
    }
}
