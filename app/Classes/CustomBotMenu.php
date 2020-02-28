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

trait CustomBotMenu
{
    protected $bot;

    protected $keyboard;

    protected $keyboard_promotions;

    protected $keyboard_friends;

    protected $keyboard_payments;

    protected $keyboard_faq;

    protected $keyboard_achievements;

    protected $keyboard_lottery;

    protected $keyboard_fallback = [
        ["Попробовать снова"],
    ];

    protected $keyboard_conversation = [
        ["Продолжить позже"],
    ];

    public function initKeyboards()
    {
        $this->keyboard = [
            [
                "\xE2\xAD\x90Акции, скидки и мероприятия"
            ],
            [
                "\xE2\x9C\x8CПригласить друзей"
            ],
            [
                "\xF0\x9F\x92\xB5Оплатить бонусами"
            ],
            [
                "\xF0\x9F\x93\x9CПомощь"
            ],

        ];

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


    public function setBot($bot)
    {
        $this->bot = $bot;
        $this->createNewUser();
        if (env("MAINTENANCE_MODE") === true) {
            $this->fallbackMenu("Система находится на техническом обслуживании! Ориентировочное время " . env("MAINTENANCE_TIME") . " мин.");
            $this->sendPhoto("*Пока мы улучшаем наши сервисы Вы можете узнать про актаульные акции и скидки в нашем канале!*",
                "https://sun9-29.userapi.com/c858232/v858232349/173635/lTlP7wMcZEA.jpg",
                [
                    [
                        ["text" => "\xF0\x9F\x91\x89Перейти в канал", "url" => env("CHANNEL_LINK")]
                    ]
                ]);
            exit;
        }
        return $this;
    }

    protected function createNewUser()
    {

        $id = $this->bot->getUser()->getId() ?? null;
        $username = $this->bot->getUser()->getUsername();
        $lastName = $this->bot->getUser()->getLastName();
        $firstName = $this->bot->getUser()->getFirstName();

        if ($id == null)
            return false;

        if ($this->getUser() == null) {
            $user = User::create([
                'name' => $username ?? "$id",
                'email' => "$id@t.me",
                'password' => bcrypt($id),
                'fio_from_telegram' => "$firstName $lastName",
                'source' => "000",
                'telegram_chat_id' => $id,
                'referrals_count' => 0,
                'referral_bonus_count' => 10,
                'cashback_bonus_count' => 0,
                'is_admin' => false,
            ]);

            event(new AchievementEvent(AchievementTriggers::MaxReferralBonusCount, 10, $user));
            return true;


        }

        if (!$this->getUser()->onRefferal()) {
            $skidobot = User::where("email", "skidobot@gmail.com")->first();
            if ($skidobot) {
                $skidobot->referrals_count += 1;
                $skidobot->save();

                $user = $this->getUser();
                $user->parent_id = $skidobot->id;
                $user->save();
            }

        }
        return false;
    }

    public function getUser(array $params = [])
    {
        return (count($params) == 0 ?
                User::where("telegram_chat_id", $this->getChatId())->first() :
                User::with($params)->where("telegram_chat_id", $this->getChatId())->first()) ?? null;

    }

    public function reply($message)
    {
        $this->sendMessage($message);
    }

    protected function getChatId()
    {
        return ($this->bot->getUser())->getId();
    }

    public function pagination($command, $model, $page, $resultMessage)
    {
        $inline_keyboard = [];

        if ($page == 0 && count($model) == config("bot.results_per_page"))
            array_push($inline_keyboard, ['text' => __("messages.action_next_btn"), 'callback_data' => "$command " . ($page + 1)]);
        if ($page > 0) {
            if (count($model) == 0) {
                array_push($inline_keyboard, ['text' => __("messages.action_prew_btn"), 'callback_data' => "$command " . ($page - 1)]);
            }
            if (count($model) == config("bot.results_per_page")) {
                array_push($inline_keyboard, ['text' => __("messages.action_prew_btn"), 'callback_data' => "$command " . ($page - 1)]);
                array_push($inline_keyboard, ['text' => __("messages.action_next_btn"), 'callback_data' => "$command " . ($page + 1)]);
            }
            if (count($model) > 0 && count($model) < config("bot.results_per_page")) {
                array_push($inline_keyboard, ['text' => __("messages.action_prew_btn"), 'callback_data' => "$command " . ($page - 1)]);
            }
        }

        if (count($inline_keyboard) > 0)
            $this->sendMessage($resultMessage, [$inline_keyboard]);

    }

    protected function sendMessage($message, array $keyboard = [], $parseMode = 'Markdown')
    {
        $callback = Telegram::sendMessage([
            "chat_id" => $this->getChatId(),
            "text" => $message,
            'parse_mode' => $parseMode,
            'reply_markup' => json_encode([
                'inline_keyboard' => $keyboard
            ])
        ]);

       /* $this->bot->sendRequest("sendMessage",
            [
                "chat_id" => $this->getChatId(),
                "text" => $message,
                'parse_mode' => $parseMode,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $keyboard
                ])
            ]);*/

      /*  Telegram::editMessageReplyMarkup([
            'chat_id' => $this->getChatId(),
            "message_id" => $callback["message_id"],
        ]);*/
    }

    protected function editMessageText($text = "empty")
    {
        $messageId = $this->bot->getMessage()->getPayload()["message_id"];

        Telegram::editMessageText([
            'text' => $text,
            'chat_id' => $this->getChatId(),
            "message_id" => $messageId
        ]);
    }

    protected function editMessageKeyboard($keyboard = [],$messageId = null)
    {
      //  $messageId = $messageId?? $this->bot->getMessage()->getPayload()["message_id"];


     /*  $r =  Telegram::editMessageReplyMarkup([
            'chat_id' => $this->getChatId(),
            "message_id" => $messageId,
            'reply_markup' => json_encode([
                'inline_keyboard' => $keyboard,
            ])
        ]);*/

        Telegram::editMessageCaption([
            'chat_id' => $this->getChatId(),
            "message_id" => $messageId,
            "caption" => "TEST TEST",
            'reply_markup' => json_encode([
                'inline_keyboard' => $keyboard,
            ])

        ]);
    }

    protected function sendMessageToChat($chatId, $message, array $keyboard = [], $parseMode = 'Markdown')
    {
        $this->bot->sendRequest("sendMessage",
            [
                "chat_id" => $chatId,
                "text" => $message,
                'parse_mode' => $parseMode,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $keyboard
                ])
            ]);
    }

    protected function sendPhoto($message, $photoUrl, array $keyboard = [], $parseMode = 'Markdown')
    {
       /* $resizedImage = Image::make($photoUrl);
        $resizedImage->resize(800, 600, function($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });*/

       $this->bot->sendRequest("sendPhoto",
            [
                "chat_id" => $this->getChatId(),
                "photo" =>  $photoUrl,
                "caption" => $message,
                'parse_mode' => $parseMode,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $keyboard,
                ])
            ]);


    }

    protected function sendLocation($latitude, $longitude, array $keyboard = [])
    {
        $this->bot->sendRequest("sendLocation",
            [
                "chat_id" => $this->getChatId(),
                "latitude" => $latitude,
                "longitude" => $longitude,
                'reply_markup' => json_encode([
                    'inline_keyboard' => $keyboard,
                ])
            ]);
    }

    protected function sendMenu($message, $keyboard)
    {

        $this->bot->sendRequest("sendMessage", [
            "text" => $message,
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'keyboard' => $keyboard,
                'one_time_keyboard' => false,
                'resize_keyboard' => true
            ])
        ]);
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

    public function mainMenu($message)
    {
        $this->initKeyboards();
        $this->sendMenu($message, $this->keyboard);
    }

    public function conversationMenu($message)
    {
        $this->sendMenu($message, $this->keyboard_conversation);
    }

    public function fallbackMenu($message)
    {
        $this->sendMenu($message, $this->keyboard_fallback);
    }

    protected function sendPhotoToChanel($message, $photoUrl, $parseMode = 'Markdown')
    {
        Telegram::sendPhoto([
            'chat_id' => env("CHANNEL_ID"),
            'parse_mode' => $parseMode,
            'caption' => $message,
            'photo' => InputFile::create($photoUrl),
            'disable_notification' => 'true'
        ]);
    }

    public function startMenuWithCategories()
    {
        $this->sendMenu(__("messages.menu_title_8"),
            $this->keyboard);

        $categories = Category::orderBy('id', 'DESC')
            ->orderBy('position', 'DESC')
            ->get();

        if (count($categories) == 0) {
            $this->reply(__("messages.promo_message_1"));
            return;
        }
        $keyboard = [];

        foreach ($categories as $cat)
            array_push($keyboard, [
                ["text" => $cat->title, "callback_data" => "/category " . $cat->id]
            ]);

        $this->sendMessage(__("messages.promo_message_2"), $keyboard);
    }

    public function startConversation($conversaton)
    {
        $this->bot->startConversation($conversaton);
    }
}
