<?php

namespace App\Models\SkidkaServiceModels;

use App\Models\User;
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
        return $this->hasOne(User::class,'id','user_sender_id');
    }

    public function recipient()
    {
        return $this->hasOne(User::class,'id',"user_recipient_id");
    }
}
