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
        'position'
    ];

    public function promotions()
    {
        return $this->hasMany('App\Promotion');
    }

    public function prizes()
    {
        return $this->hasMany('App\Prize',"id","company_id");
    }

    public function promocodes()
    {
        return $this->hasMany('App\Promocode',"id","company_id");
    }
}
