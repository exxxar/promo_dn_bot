<?php

namespace App\Models\SkidkaServiceModels;

use Illuminate\Database\Eloquent\Model;

class GeoQuest extends Model
{
    //
    protected $dates = ['start_at', 'end_at','created_at','updated_at'];

    protected $fillable = [
        'title',
        'description',
        'image_url',
        'is_active',
        'promotion_id',
        'company_id',
        'reward_bonus',
        'position',
        'start_at',
        'end_at',
        'created_at',
        'updated_at'
    ];

    protected $appends = ["quest_points_list"];


    public function getQuestPointsListAttribute(){
        return QuestHasPoints::where("geo_quest_id", $this->id)
            ->get();
    }

    public function promotion()
    {
        return $this->hasOne(Promotion::class, 'id', 'promotion_id');
    }

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }
}
