<?php

namespace App\Models\SkidkaServiceModels;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CharityHistory extends Model
{
    protected $fillable = [
        'user_id',
        'charity_id',
        'company_id',
        'donated_money',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function company()
    {
        return $this->belongsTo(Company::class, "company_id", "id");
    }

    public function charity()
    {
        return $this->belongsTo(Charity::class, "charity_id", "id");
    }
}
