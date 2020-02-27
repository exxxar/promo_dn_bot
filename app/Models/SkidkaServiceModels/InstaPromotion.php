<?php

namespace App\Models\SkidkaServiceModels;

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
        return $this->belongsTo(Company::class, "company_id", "id");
    }

    public function getSummaryAttribute(){
        return UplodedPhoto::where("insta_promotions_id",$this->id)->count()??0;
    }

}
