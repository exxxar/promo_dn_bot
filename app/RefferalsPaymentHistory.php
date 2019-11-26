<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RefferalsPaymentHistory extends Model
{
    //
    protected $fillable = [
        'user_id',
        'employee_id',
        'company_id',
        'value',
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
