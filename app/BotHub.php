<?php

namespace App;

use App\Models\SkidkaServiceModels\Company;
use Illuminate\Database\Eloquent\Model;

class BotHub extends Model
{
    protected $fillable = [
        'token_prod',
        'token_dev',
        'bot_pic',
        'bot_url',
        'webhook_url',
        'description',
        'is_active',
        'money',
        'money_per_day',
        'company_id'
    ];

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }
}
