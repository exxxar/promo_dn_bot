<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstaPromotion extends Model
{
    //
    protected $fillable = [
        'photo_url',
        'title',
        'description',
        'promo_bonus',
        'position',
        'is_active',
        'company_id',
    ];

    public function company()
    {
        return $this->belongsTo('App\Company', "company_id", "id");
    }
}
