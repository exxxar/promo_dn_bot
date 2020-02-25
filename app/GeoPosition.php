<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeoPosition extends Model
{

    protected $fillable = [
        'title',
        'description',
        'image_url',
        'latitude',
        'longitude',
        'radius',
    ];
}
