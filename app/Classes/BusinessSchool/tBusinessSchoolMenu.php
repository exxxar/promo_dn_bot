<?php


namespace App\Classes\BusinessSchool;


trait tBusinessSchoolMenu
{
    protected $business_school_main_keyboard = [
        [
            "\xE2\x9B\xB5Про Школу бизнеса", "\xE2\xAD\x90Мероприятия"
        ],
        [
            "\xF0\x9F\x90\x9DБоты для Вашего бизнеса", "\xF0\x9F\x8C\x9FМаркетинг 2.0"
        ],
        [
            "\xF0\x9F\x8E\xA8Бизнес и личностный рост", "\xF0\x9F\x8E\xA2Личностный рост 4+1"
        ],
        [
            "\xF0\x9F\x91\x86Обо мне", "\xF0\x9F\x91\x94Rest Service"
        ]

    ];



    public function mainMenu($message){
        $this->sendMenu($message,$this->business_school_main_keyboard);
    }

    
}
