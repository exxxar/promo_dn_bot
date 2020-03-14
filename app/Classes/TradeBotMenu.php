<?php


namespace App\Classes;


trait TradeBotMenu
{

    protected $keyboard_basket = [
        [
            "Корзина (%s)"
        ]
    ];

    public function initKeyboards($summary){
        $this->keyboard_basket[0][0] =  sprintf($this->keyboard_basket[0][0],$summary);
    }
}
