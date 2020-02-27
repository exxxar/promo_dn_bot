<?php

namespace App\Models\SkidkaServiceModels;

use App\Enums\AchievementTriggers;
use App\Models\User;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    //
    use CastsEnums;

    protected $enumCasts = [
        // 'attribute_name' => Enum::class
        'stat_type' => AchievementTriggers::class,
    ];

    protected $fillable = [
        'stat_type',
        'stat_value',
        'user_id',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
