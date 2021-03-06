<?php

namespace App\Models\SkidkaServiceModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Company extends Model
{
    //
    protected $fillable = [
        'title',
        'address',
        'description',
        'phone',
        'email',
        'bailee',
        'cashback',//добавить в бд
        'logo_url',
        'position',
        'telegram_bot_url',
        'is_active',
        'menu_url',
        'lottery_start_price',
        'quest_expire_time'
    ];

    public function getPromotionsSortedByPosition()
    {
        return $this->promotions()->orderBy('position', 'DESC')->get();
    }

    public function uniqCategories()
    {
        $tmp = "";

        $promos = $this->promotions()->get()->unique("category_id");

        foreach ($promos as $promo) {
            $tmp .= "#" . ($promo->category()->first()->title ?? '') . ",";
        }
        $tmp = substr($tmp, 0, strlen($tmp) - 1);

        return $tmp;
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class, "company_id", "id");
    }

    public function prizes()
    {
        return $this->hasMany(Prize::class, "company_id", "id");
    }

    public function promocodes()
    {
        return $this->hasMany(Promocode::class, "company_id", "id");
    }

    public function getActivePromotions($chatId)
    {
        return array_filter($this->promotions()->get(), function ($item) use ($chatId) {
            return $item->isNotActiveByUser($chatId);
        });
    }

    public function hasPrizes()
    {
        return $this->prizes()->count() > 0;
    }


}
