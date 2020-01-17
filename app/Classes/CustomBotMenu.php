<?php


namespace App\Classes;


trait CustomBotMenu
{
    protected $keyboard = [
        ["\xE2\x9B\x84Мероприятия","\xF0\x9F\x94\xA5Акции"],
        ["\xF0\x9F\x93\xB2Мои друзья", "\xF0\x9F\x92\xB3Мои баллы"],
        ["\xE2\x9D\x93F.A.Q."],

    ];

    protected $keyboard_admin = [
        ["\xE2\x9B\x84Мероприятия","\xF0\x9F\x94\xA5Акции"],
        ["\xF0\x9F\x93\xB2Мои друзья", "\xF0\x9F\x92\xB3Мои баллы"],
        ["\xE2\x9D\x93F.A.Q."],

    ];

    protected $keyboard_fallback = [
        ["Попробовать снова"],
    ];

    protected $keyboard_conversation = [
        ["Продолжить позже"],
    ];
}
