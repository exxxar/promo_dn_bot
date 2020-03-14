<?php


namespace App\Classes;


use App\Models\SkidkaServiceModels\Category;
use App\Enums\AchievementTriggers;
use App\Events\AchievementEvent;
use App\User;
use BotMan\Drivers\Telegram\TelegramDriver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;

trait SkidkiBotMenu
{


    protected $keyboard_promotions;

    protected $keyboard_friends;

    protected $keyboard_payments;

    protected $keyboard_faq;

    protected $keyboard_achievements;

    protected $keyboard_lottery;

    protected $keyboard_geo_quest;


    public function initKeyboards()
    {

        $this->keyboard_promotions = [
            [
                "\xF0\x9F\x8F\xACАкции по компаниям", "\xF0\x9F\x8E\xADАкции по категориям",
            ],
            [
                "\xF0\x9F\x8E\xAAНаши мероприятия", "\xF0\x9F\x8E\xB4Акции в Instagram",
            ],
            [
                "\xF0\x9F\x8E\xAFСистема достижений", "\xF0\x9F\x8E\xB0Розыгрыши"
            ],
            [
                "\xF0\x9F\x8C\x8FСистема Гео-квестов"
            ],
            [
                "\xF0\x9F\x94\x99Главное меню"
            ]
        ];

        $this->keyboard_friends = [
            [
                "\xF0\x9F\x8C\x90Поделиться в соц. сетях",
            ],
            [
                "\xF0\x9F\x91\xAAПосмотреть моих друзей",
            ],
            [
                "\xF0\x9F\x94\x99Главное меню"
            ]
        ];

        $this->keyboard_geo_quest = [
            [
                ["text" => "\xF0\x9F\x8C\x8DОтправить мою гео-локацию", "request_location" => true]
            ],
            [
                "\xF0\x9F\x94\x9BБлижайшие ко мне квесты",
            ],
            [
                "\xF0\x9F\x93\x9CМоё прохождение квестов",
            ],
            [
                "\xF0\x9F\x94\x99Акции, скидки и мероприятия"
            ],
            [
                "\xF0\x9F\x94\x99Главное меню"
            ]
        ];

        $this->keyboard_payments = [
            [
                "\xF0\x9F\x92\xB5CashBack по компаниям",
            ],
            [
                "\xF0\x9F\x93\xB2Начисления CashBack",
            ],
            [
                "\xF0\x9F\x92\xB8Списания CashBack",
            ],
            [
                "\xF0\x9F\x93\x8BСтатистика активности",
            ],
            [
                "\xF0\x9F\x8C\xBCБлаготворительность"
            ],
            [
                "\xF0\x9F\x94\x99Главное меню"
            ]
        ];

        $this->keyboard_faq = [
            [
                "\xF0\x9F\x93\x92Полезная информация",
            ],
            [
                "\xF0\x9F\x93\x8DКак пользоваться",
            ],
            [
                "\xE2\x9A\xA0Соглашение на обработку данных"
            ],
            [
                "\xF0\x9F\x94\x99Главное меню"
            ]
        ];

        $this->keyboard_lottery = [
            [
                "\xF0\x9F\x94\xA5Ввести код и начать", "\xF0\x9F\x92\xB8Участвовать за CashBack",
            ],
            [
                "\xF0\x9F\x8E\x81Промокод в подарок",
            ],
            [
                "\xF0\x9F\x94\x99Акции, скидки и мероприятия"
            ],
            [
                "\xF0\x9F\x94\x99Главное меню"
            ]
        ];

        $this->keyboard_achievements = [
            [
                "\xE2\x9C\xA8Все достижения", "\xF0\x9F\x92\xAAМои достижения",
            ],
            [
                "\xF0\x9F\x94\x99Акции, скидки и мероприятия"
            ],
            [
                "\xF0\x9F\x94\x99Главное меню"
            ]
        ];
    }


    public function questMenu($message)
    {
        $this->initKeyboards();
        $this->sendMenu($message, $this->keyboard_geo_quest);
    }


    public function faqMenu($message)
    {
        $this->initKeyboards();
        $this->sendMenu($message, $this->keyboard_faq);
    }

    public function lotteryMenu($message)
    {
        $this->initKeyboards();
        $this->sendMenu($message, $this->keyboard_lottery);
    }

    public function achievementsMenu($message)
    {
        $this->initKeyboards();
        $this->sendMenu($message, $this->keyboard_achievements);
    }

    public function paymentMenu($message)
    {
        $this->initKeyboards();
        $this->sendMenu($message, $this->keyboard_payments);
    }

    public function friendsMenu($message)
    {
        $this->initKeyboards();
        $this->sendMenu($message, $this->keyboard_friends);
    }

    public function promotionsMenu($message)
    {
        $this->initKeyboards();
        $this->sendMenu($message, $this->keyboard_promotions);
    }


}
