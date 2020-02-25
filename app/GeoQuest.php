<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeoQuest extends Model
{
    //
    protected $dates = ['start_at','end_at'];

    protected $fillable = [
        'title',
        'description',
        'image_url',
        'is_active',
        'promotion_id',
        'reward_bonus',
        'position',
        'start_at',
        'end_at'
    ];

    protected $appends = ["quest_sequence_count"];

    public function getQuestSequenceCountAttribute()
    {
        return QuestHasPoints::where("geo_quest_id", $this->id)
            ->count();
    }

    public function promotion()
    {
        return $this->hasOne('App\Promotion', 'id', 'promotion_id');
    }
}
