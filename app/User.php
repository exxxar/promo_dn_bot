<?php

namespace App;

use App\Enums\AchievementTriggers;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Log;
use Kyslik\ColumnSortable\Sortable;

class User extends Authenticatable
{
    use Notifiable,Sortable;

    public $sortable = ['id'];

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
        'updated_at',
        'created_at',


        'network_cashback_bonus_count',
        'network_friends_count',

        'current_network_level',
        'activated',
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
        return $this->hasMany('App\Stat', 'user_id', 'id');
    }

    public function parent()
    {
        return $this->hasOne('App\User', 'id', 'parent_id');
    }

    public function childs()
    {
        return $this->hasMany('App\User', 'parent_id', 'id');
    }

    public function achievements()
    {
        return $this->belongsToMany('App\Achievement', 'user_has_achievements', 'user_id', 'achievement_id')
            ->withTimestamps();
    }

    public function spentCashBack()
    {
        return $this->hasMany('App\RefferalsPaymentHistory', 'user_id', 'id');
    }

    public function getSummaryAttribute()
    {
        $stat_1 = $this->stats()->where("stat_type", AchievementTriggers::MaxCashBackCount)->first();
        $stat_2 = $this->stats()->where("stat_type", AchievementTriggers::MaxReferralBonusCount)->first();
        return ($stat_1==null?0:$stat_1->stat_value)+($stat_2==null?0:$stat_2->stat_value);
    }

    public function getSpentAttribute()
    {
        $sum = 0;
        foreach ($this->spentCashBack as $sp) {
            $sum += $sp->value;
        }
        return $sum;
    }


}
