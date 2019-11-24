<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashbackHistory extends Model
{
    protected $fillable = [
        'money_in_check',
        'activated',
        'employee_id',
    ];

    public function employee()
    {
        return $this->hasOne('App\User','employee_id','id');
    }
}
