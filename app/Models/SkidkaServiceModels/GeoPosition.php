<?php

namespace App\Models\SkidkaServiceModels;

use Illuminate\Database\Eloquent\Model;

class GeoPosition extends Model
{


    protected $fillable = [
        'title',
        'description',
        'image_url',
        'latitude',
        'longitude',
        'radius',
        'local_promotion_id',
        'local_reward',
        'in_time_range',
        'range_time_value',
        'time_end',
        'time_start',
    ];

    public function promotion()
    {
        return $this->hasOne(Promotion::class, 'id', 'local_promotion_id');
    }

    public function isActive()
    {
        return strtotime(date("G:i")) <= strtotime($this->time_end) &&
            strtotime(date("G:i")) >= strtotime($this->time_start);
    }

    public static function getNearestQuestPoints($latitude, $longitude)
    {
        $dist = env('GEO_QUEST_GLOBAL_DISTANCE'); #дистанция 20 км

        $lon1 = $longitude - $dist / abs(cos(rad2deg($latitude)) * 111.0); # 1 градус широты = 111 км
        $lon2 = $longitude + $dist / abs(cos(rad2deg($latitude)) * 111.0);
        $lat1 = $latitude - ($dist / 111.0);
        $lat2 = $latitude + ($dist / 111.0);

        return GeoPosition::whereBetween('latitude', [$lat1, $lat2])
            ->whereBetween('longitude', [$lon1, $lon2])
            ->get();


    }

    public function inRange($latitude, $longitude)
    {

        $dist = $this->radius;

        $lon1 = $longitude - $dist / abs(cos(rad2deg($latitude)) * 111.0); # 1 градус широты = 111 км
        $lon2 = $longitude + $dist / abs(cos(rad2deg($latitude)) * 111.0);
        $lat1 = $latitude - ($dist / 111.0);
        $lat2 = $latitude + ($dist / 111.0);

        $position = GeoPosition::whereBetween('latitude', [$lat1, $lat2])
            ->whereBetween('longitude', [$lon1, $lon2])
            ->where('id', $this->id)
            ->first();

        return !is_null($position);

        /// $profiles = UserProfile.objects.filter(lat__range=(lat1, lat2)).filter(lon__range=(lon1, lon2))

                /*
                SET @lat = 51.526613503445766; # дано в условии
        SET @lng = 46.02093849218558;
        SET @half= [10 км в радианах] / 2 ;


        SELECT id
        FROM points
        WHERE lat BETWEEN @lat - @half AND @lat + @half
                AND lng BETWEEN @lng - @half AND @lng + @half;*/
    }
}
