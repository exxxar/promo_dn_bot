<?php

namespace App\Models\SkidkaServiceModels;

use App\User;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class CashBackInfo extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'value',
        'quest_bonus',
        'quest_begin_at',
        'quest_reset_at'
    ];

    protected $hidden = ['quest_bonus', 'quest_begin_at', 'quest_reset_at'];

    protected $appends = ['current_quest_bonus'];

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    private function isExpired()
    {
        $time_0 = (date_timestamp_get(new DateTime($this->quest_begin_at)));
        $time_1 = (date_timestamp_get(new DateTime($this->quest_reset_at)));
        $time_2 = date_timestamp_get(now());
        return !($time_2 >= $time_0 && $time_2 < $time_1);
    }

    public function getCurrentQuestBonusAttribute()
    {
        if ($this->isExpired()) {
            $this->quest_bonus = 0;
            $this->save();
        }
        
        return $this->quest_bonus;
    }

}
