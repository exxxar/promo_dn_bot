<?php

namespace App\Models\SkidkaServiceModels;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Promocode extends Model
{
    protected $fillable = [
        'code',
        'activated',
        'user_id',
        'company_id',
        'prize_id',
        'prize_has_taken',

    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function prize()
    {
        return $this->hasOne(Prize::class, 'id', 'prize_id');
    }

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }
}
