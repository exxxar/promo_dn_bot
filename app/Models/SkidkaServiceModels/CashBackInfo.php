<?php

namespace App\Models\SkidkaServiceModels;

use App\Models\User;
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
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
