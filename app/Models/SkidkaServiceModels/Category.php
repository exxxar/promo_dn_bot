<?php

namespace App\Models\SkidkaServiceModels;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $fillable = [
        'title',
        'description',
        'image_url',
        'created_at',
        'updated_at',
        'position'
    ];

    public function promotions()
    {
        return $this->hasMany(Promotion::class, "category_id", "id");
    }

    public function getActivePromotions($chatId)
    {
        return array_filter($this->promotions()->get(), function ($item) use ($chatId) {
            return $item->isNotActiveByUser($chatId);
        });
    }

}
