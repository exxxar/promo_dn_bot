<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'fio_from_telegram',
        'fio_from_request',
        'phone',
        'avatar_url',
        'address',
        'sex',
        'birthday',
        'age',
        'source',
        'telegram_chat_id',
        'referrals_count',
        'referral_bonus_count',
        'cashback_bonus_count',
        'is_admin',
        'parent_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',


    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function promos()
    {
        return $this->belongsToMany('App\Promotion', 'user_has_promos', 'user_id', 'promotion_id')
            ->withTimestamps();
    }

    public function companies()
    {
        return $this->belongsToMany('App\Company', 'user_in_companies', 'user_id', 'company_id')
            ->withTimestamps();
    }

    public function stats()
    {
        return $this->hasMany('App\Stat', 'id', 'user_id');
    }

    public function parent()
    {
        return $this->hasOne('App\User','parent_id','id');
    }

    public function childs()
    {
        return $this->hasMany('App\User','parent_id','id');
    }

    public function achievements()
    {
        return $this->belongsToMany('App\Achievement', 'user_achievement', 'user_id', 'achievement_id')
            ->withTimestamps();
    }
}
