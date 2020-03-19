<?php

return [
    'business_school_bot'=>[
        "/start|Попробовать снова" => "start",
        "/.*Про Школу бизнеса" => "getAboutBusinessSchoolPage",
        "/.*Мероприятия" => "getEventsPage",
        "/.*Боты для Вашего бизнеса" => "getBotForBusinessPage",
        "/.*Маркетинг 2.0" => "aboutBusinessSchool",
        "/.*Бизнес и личностный рост" => "getBusinessPersonalGrowthPage",
        "/.*Личностный рост 4\+1" => "getPersonalGrowthPage",
        "/.*Обо мне" => "getAboutMePage",
        "/.*Rest Service" => "getRestServicePage",
        "/edit_events_page ([0-9]+)" => "editEventsPage",
    ]
];
