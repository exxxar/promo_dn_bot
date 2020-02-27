<?php

namespace App\Models\SkidkaServiceModels;

use App\Models\User;
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
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function employee()
    {
        return $this->hasOne(User::class, 'id', 'employee_id');
    }

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }


}
