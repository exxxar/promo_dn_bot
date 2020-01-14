<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promocode extends Model
{
    protected $fillable = [
        'code',
        'activated',
        'user_id',
        'company_id',
        'prize_id',
        'prize_has_taken',

    ];

    public function user()
    {
        return $this->hasOne('App\User','id','user_id');
    }

    public function prize()
    {
        return $this->hasOne('App\Prize','id','prize_id');
    }

    public function company()
    {
        return $this->hasOne('App\Company','id','company_id');
    }
}
