<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    //
    protected $fillable = [
        'title',
        'description',
        'event_image_url',
        'start_at',
        'end_at',
        'company_id'
    ];

    public function company()
    {
        return $this->belongsTo('App\Company', "company_id", "id");
    }
}
