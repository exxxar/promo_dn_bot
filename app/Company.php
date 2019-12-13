<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    //
    protected $fillable = [
        'title',
        'address',
        'description',
        'phone',
        'email',
        'bailee',
        'cashback',//добавить в бд
        'logo_url',
    ];

    public function promotions()
    {
        return $this->hasMany('App\Promotion');
    }
}
