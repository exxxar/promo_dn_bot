<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CharityHistory extends Model
{
    protected $fillable = [
        'user_id',
        'charity_id',
        'company_id',
        'donated_money',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', "user_id", "id");
    }

    public function company()
    {
        return $this->belongsTo('App\Company', "company_id", "id");
    }

    public function charity()
    {
        return $this->belongsTo('App\Charity', "charity_id", "id");
    }
}
