<?php

namespace App\Models\SkidkaServiceModels;

use Illuminate\Database\Eloquent\Model;

class UserHasAchievement extends Model
{

    public function achievement()
    {
        return $this->hasOne(Achievement::class, 'id', 'user_id');
    }
}
