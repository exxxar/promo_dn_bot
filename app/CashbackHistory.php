<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashbackHistory extends Model
{
    protected $fillable = [
        'money_in_check',
        'activated',
        'employee_id',
        'check_info',
        'user_phone',
        'created_at'

    ];


    public function employee()
    {
        return $this->hasOne('App\User','id','employee_id');
    }
}
