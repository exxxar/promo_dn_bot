<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image_url',
        'company_id',

        'summary_activation_count',
        'current_activation_count',

        'is_active',
    ];

    public function company()
    {
        return $this->hasOne('App\Company', 'id', 'company_id');
    }
}
