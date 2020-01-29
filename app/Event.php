<?php

namespace App;

use App\Enums\AchievementTriggers;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    //
    protected $fillable = [
        'title',
        'description',
        'event_image_url',
        'start_at',
        'end_at',
        'company_id',
        'position'
    ];

    public function company()
    {
        return $this->belongsTo('App\Company', "company_id", "id");
    }

    public function promotion()
    {
        return $this->belongsTo('App\Promotion', "promo_id", "id");
    }

    public function isActive()
    {
        $time_0 = (date_timestamp_get(new DateTime($this->start_at)));
        $time_1 = (date_timestamp_get(new DateTime($this->end_at)));
        $time_2 = date_timestamp_get(now());
        return ($time_2 >= $time_0 && $time_2 < $time_1);
    }


}
