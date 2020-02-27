<?php

namespace App\Models\SkidkaServiceModels;

use Illuminate\Database\Eloquent\Model;

class QuestHasPoints extends Model
{
    //
    protected $fillable = [
        'position',
        'is_last'
    ];
}
