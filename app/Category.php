<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $fillable = [
        'title',
        'description',
        'image_url',
        'created_at',
        'updated_at',
        'position'
    ];

    public function promotions()
    {
        return $this->hasMany('App\Promotion');
    }
}
