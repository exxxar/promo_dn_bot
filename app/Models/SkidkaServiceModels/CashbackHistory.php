<?php

namespace App\Models\SkidkaServiceModels;

use App\Models\User;
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
        return $this->hasOne(User::class,'id','user_id');
    }

    public function employee()
    {
        return $this->hasOne(User::class,'id','employee_id');
    }

    public function company()
    {
        return $this->hasOne(Company::class,'id','company_id');
    }
}
