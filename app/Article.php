<?php

namespace App;

use App\Enums\AchievementTriggers;
use App\Enums\Parts;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    //
    protected $enumCasts = [
        'part' => Parts::class,
    ];

    protected $fillable = [
        'url',
        'part',
        'is_visible',
    ];
}
