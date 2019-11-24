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
        'logo_url',
    ];

    public function promotions()
    {
        return $this->hasMany('App\Promotion');
    }
}
