<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RefferalsHistory extends Model
{
    //
    protected $fillable = [
        'user_sender_id',
        'user_recipient_id',
        'activated',
    ];

    public function sender()
    {
        return $this->hasOne('App\User','user_sender_id','id');
    }

    public function recipient()
    {
        return $this->hasOne('App\User','user_recipient_id','id');
    }
}
