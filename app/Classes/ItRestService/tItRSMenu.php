<?php


namespace App\Classes\ItRestService;


trait tItRSMenu
{
    protected $itrs_main_keyboard = [
        [
            "\xE2\x9B\xB5Наши проекты"
        ],
        [
            "\xF0\x9F\x90\x9DБоты для Вашего бизнеса"
        ],
        [
            "\xF0\x9F\x8E\xA8Наши услуги"
        ],
       [
           "\xE2\xAD\x90О Нас"
       ]

    ];



    public function mainMenu($message){
        $this->sendMenu($message,$this->itrs_main_keyboard);
    }


}
