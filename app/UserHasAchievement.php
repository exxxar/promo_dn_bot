<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserHasAchievement extends Model
{


    protected $fillable = [
        'activated',
        'user_id',
        'achievement_id'
    ];

    public function achievement()
    {
        return $this->hasOne('App\Achievement', 'id', 'user_id');
    }
}
