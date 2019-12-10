<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserHasAchievement extends Model
{

    public function achievement()
    {
        return $this->hasOne('App\Achievement', 'id', 'user_id');
    }
}
