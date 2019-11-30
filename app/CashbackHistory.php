<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashbackHistory extends Model
{
    protected $fillable = [
        'money_in_check',
        'activated',
        'employee_id',
        'user_id',
        'company_id',
        'check_info',
        'user_phone',
        'created_at'

    ];

    public function user()
    {
        return $this->hasOne('App\User','id','user_id');
    }

    public function employee()
    {
        return $this->hasOne('App\User','id','employee_id');
    }

    public function company()
    {
        return $this->hasOne('App\Company','id','company_id');
    }
}
