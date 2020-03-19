<?php


namespace App\Classes\BusinessSchool;


trait tBrainAuctionMenu
{
    protected $brain_auction_main_keyboard = [
        [
            "Аукцион работы", "Разместить заказ"
        ],
        [
            "О проекте"
        ]

    ];




    public function mainMenu($message)
    {
        $this->sendMenu($message, $this->brain_auction_main_keyboard);
    }


}
