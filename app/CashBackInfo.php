<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashBackInfo extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'value',
    ];

    public function company()
    {
        return $this->hasOne('App\Company', 'id', 'company_id');
    }

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
}
