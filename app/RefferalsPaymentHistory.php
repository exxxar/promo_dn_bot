<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RefferalsPaymentHistory extends Model
{
    //
    protected $fillable = [
        'user_id',
        'employee_id',
        'value',
    ];

    public function user()
    {
        return $this->hasOne('App\User','user_id','id');
    }

    public function employee()
    {
        return $this->hasOne('App\User','employee_id','id');
    }


}
