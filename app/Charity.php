<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Charity extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image_url',
        'is_active',
        'position',
    ];

    protected $appends = ['donates'];

    public function getDonatesAttribute()
    {
        $chs = CharityHistory::where("charity_id", $this->id)
                ->get() ?? null;


        $sum = 0;
        if (count($chs) > 0)
            foreach ($chs as $ch)
                $sum += $ch->donated_money;

        return $sum;
    }
}
