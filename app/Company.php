<?php

namespace App;

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
        'telegram_bot_url'
    ];

    public function uniqCategories()
    {
        $tmp = "";
        try {


            $promos = $this->promotions()->get()->unique("category_id");

            foreach ($promos as $promo) {
                $tmp .= "#" . $promo["category"]["title"] . ",";
            }
            $tmp = substr($tmp, 0, strlen($tmp) - 1);
        } catch (\Exception $e) {

        }
        return $tmp;
    }

    public function promotions()
    {
        return $this->hasMany('App\Promotion', "company_id", "id");
    }

    public function prizes()
    {
        return $this->hasMany('App\Prize', "company_id", "id");
    }

    public function promocodes()
    {
        return $this->hasMany('App\Promocode', "company_id", "id");
    }

    public function getActivePromotions($chatId)
    {
        return array_filter($this->promotions()->get(), function ($item) use ($chatId) {
            return $item->isNotActiveByUser($chatId);
        });
    }
}
