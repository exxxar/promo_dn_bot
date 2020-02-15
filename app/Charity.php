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
        return (CharityHistory::where("charity_id",$this->id)
            ->first())->donated_money;
    }
}
