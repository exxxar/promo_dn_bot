<?php

namespace App;

use App\Enums\AchievementTriggers;
use App\Models\SkidkaServiceModels\Achievement;
use App\Models\SkidkaServiceModels\CashbackHistory;
use App\Models\SkidkaServiceModels\CashBackInfo;
use App\Models\SkidkaServiceModels\Company;
use App\Models\SkidkaServiceModels\Promotion;
use App\Models\SkidkaServiceModels\RefferalsHistory;
use App\Models\SkidkaServiceModels\RefferalsPaymentHistory;
use App\Models\SkidkaServiceModels\Stat;
use DateTime;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Log;
use Kyslik\ColumnSortable\Sortable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, Sortable,HasApiTokens;

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
        'instagram',
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
        return $this->belongsToMany(Promotion::class, 'user_has_promos', 'user_id', 'promotion_id')
            ->withTimestamps()
            ->withPivot('user_activation_count');
    }

    public function onPromos()
    {
        //todo: on promos
        return $this->promos()->count()>0&&$this->promos()->first()->pivot->user_activation_count==0;
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'user_in_companies', 'user_id', 'company_id')
            ->withTimestamps();
    }

    public function stats()
    {
        return $this->hasMany(Stat::class, 'user_id', 'id');
    }

    public function cashbackinfos()
    {
        return $this->hasMany(CashBackInfo::class, 'user_id', 'id');
    }

    public function parent()
    {
        return $this->hasOne(User::class, 'id', 'parent_id');
    }

    public function childs()
    {
        return $this->hasMany(User::class, 'parent_id', 'id');
    }

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_has_achievements', 'user_id', 'achievement_id')
            ->withTimestamps();
    }

    public function spentCashBack()
    {
        return $this->hasMany(RefferalsPaymentHistory::class, 'user_id', 'id');
    }

    public function getSummaryAttribute()
    {
        $stat_1 = $this->stats()->where("stat_type", AchievementTriggers::MaxCashBackCount)->first();
        $stat_2 = $this->stats()->where("stat_type", AchievementTriggers::MaxReferralBonusCount)->first();
        return ($stat_1 == null ? 0 : $stat_1->stat_value) + ($stat_2 == null ? 0 : $stat_2->stat_value);
    }

    public function getSpentAttribute()
    {
        $sum = 0;
        foreach ($this->spentCashBack as $sp) {
            $sum += $sp->value;
        }
        return $sum;
    }

    public function isActive()
    {
        $time_0 = (date_timestamp_get(new DateTime($this->start_at)));
        $time_1 = (date_timestamp_get(new DateTime($this->end_at)));
        $time_2 = date_timestamp_get(now());
        return ($time_2 >= $time_0 && $time_2 < $time_1);
    }

    public function hasPhone()
    {
        return $this->phone != null;
    }

    public function onRefferal()
    {
        return RefferalsHistory::where("user_recipient_id", $this->id)->first() != null;
    }

    public function getFriends($page)
    {
        return RefferalsHistory::with(["recipient"])
            ->where("user_sender_id", $this->id)
            ->skip($page * config("bot.results_per_page"))
            ->take(config("bot.results_per_page"))
            ->orderBy('id', 'DESC')
            ->get();
    }

    public function getPayments($page)
    {
        return RefferalsPaymentHistory::with(["company"])
            ->where("user_id", $this->id)
            ->skip($page * config("bot.results_per_page"))
            ->take(config("bot.results_per_page"))
            ->orderBy('id', 'DESC')
            ->get();
    }

    public function getCashBacksByUserId($page)
    {
        return CashbackHistory::where("user_id", $this->id)
            ->skip($page * config("bot.results_per_page"))
            ->take(config("bot.results_per_page"))
            ->orderBy('id', 'DESC')
            ->get();
    }

    public function getLatestCashBack()
    {
        return CashbackHistory::where("user_phone", $this->phone)
            ->where("activated", false)
            ->get();
    }

    public function getCashBacksByPhone($page)
    {
        return CashbackHistory::where("user_phone", $this->phone)
            ->skip($page * config("bot.results_per_page"))
            ->take(config("bot.results_per_page"))
            ->orderBy('id', 'DESC')
            ->get();
    }

    public function getAchievements($page)
    {
        return $this->achievements()
                ->skip($page * config("bot.results_per_page"))
                ->take(config("bot.results_per_page"))
                ->orderBy('id', 'DESC')
                ->get() ?? null;
    }

    public function getStats()
    {
        return Stat::where("user_id", $this->id)
            ->get();

    }
}
