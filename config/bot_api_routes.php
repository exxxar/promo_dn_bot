<?php

return [
    'test_api_bot'=>[
        "/start" => "menu",
        "/start ([0-9]+) ([0-9]+)" => "menu2",
        "/.*Про Школу бизнеас" => "getAboutBusinessSchoolPage",
        "/.*Мероприятия" => "getEventsPage",
        "/.*Боты для Вашего бизнеса" => "getBotForBusinessPage",
        "/.*Маркетинг 2.0" => "aboutBusinessSchool",
        "/.*Бизнес и личностный рост" => "getBusinessPersonalGrowthPage",
        "/.*Личностный рост 4+1" => "getPersonalGrowthPage",
        "/.*Обо мне" => "getAboutMePage",
        "/.*Rest Service" => "getRestServicePage",
    ]
];
