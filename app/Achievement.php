<?php

namespace App;

use App\Enums\AchievementTriggers;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;

class Achievement extends Model
{
    use CastsEnums;



    protected $enumCasts = [
          'trigger_type' => AchievementTriggers::class,
    ];

    protected $fillable = [
        'title',
        'description',
        'ach_image_url',
        'trigger_type',
        'trigger_value',
        'prize_description',
        'prize_image_url',
        'position',
        'is_active'
    ];

    public function users()
    {
        return $this->belongsToMany('App\User','user_achievement','achievement_id','user_id')
            ->withTimestamps();
    }

}
