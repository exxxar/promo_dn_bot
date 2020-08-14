<?php

namespace App\Models\SkidkaServiceModels;

use App\Enums\AchievementTriggers;
use App\Enums\Parts;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use CastsEnums;

    //
    protected $enumCasts = [
        'part' => Parts::class,
    ];

    protected $fillable = [
        'url',
        'part',
        'is_visible',
        'position'
    ];
}
