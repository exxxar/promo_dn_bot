<?php


namespace App\Classes;


use App\Achievement;
use App\Article;
use App\Category;
use App\Company;
use App\Drivers\TelegramInlineQueryDriver;
use App\Enums\Parts;
use App\Event;
use App\Prize;
use App\Promocode;
use App\Promotion;
use BotMan\BotMan\BotMan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SkidkiDNBot extends Bot implements iSkidkiDNBot
{

    public function getEventsAll($page)
    {
        $events = Event::with(['company'])
            ->skip($page * config("bot.results_per_page"))
            ->take(config("bot.results_per_page"))
            ->orderBy('position', 'DESC')
            ->get();

        $found = false;

        if (count($events) > 0) {

            foreach ($events as $key => $event) {

                if (!$event->isActive() || !$event->company->is_active)
                    continue;

                $keyboard = [];

                if (!is_null($event->promo_id)) {
                    $keyboard = [
                        [
                            ["text" => "\xF0\x9F\x91\x89Перейти к описанию акции", "callback_data" => "/promotion " . $event->promo_id]
                        ]
                    ];
                }
                $found = true;
                $this->sendPhoto(
                    "*" . $event->title . "*\n" . $event->description,
                    $event->event_image_url, $keyboard);

            }

        }

        if (count($events) == 0 || !$found)
            $this->reply("Мероприятия появятся в скором времени!");

        $this->pagination("/events $page", $events, $page, "Выберите действие");

    }

    public function getPaymentsAll($page)
    {
        if (!$this->getUser()->hasPhone()) {
            $keyboard = [
                [
                    ["text" => "Заполнить", "callback_data" => "/fillinfo"],
                ]
            ];

            $this->sendMessage("У вас не заполнена личная информация и вы не можете просматривать историю оплаты.", $keyboard);
            return;
        }

        $refs = $this->getUser()->getPayments($page);

        $tmp = "";

        foreach ($refs as $key => $ref)
            $tmp .= sprintf("_%s_ в %s потрачено *%s* бонусов\n",
                $ref->created_at,
                ($ref->company->title ?? $ref->company->id),
                $ref->value
            );

        $this->reply(strlen($tmp) > 0 ? $tmp : "Вы не оплачивали через систему CashBack.");
        $this->pagination("/payments", $refs, $page, (strlen($tmp) > 0 ? $tmp : "Вы не потратили свои бонусы."));
    }

    public function getCashBacksAll($page)
    {
        if (!$this->getUser()->hasPhone()) {
            $keyboard = [
                [
                    ["text" => "Заполнить", "callback_data" => "/fillinfo"],
                ]
            ];

            $this->sendMessage("У вас не заполнена личная информация и вы не можете просматривать историю оплаты.", $keyboard);
            return;
        }

        $cashbacks = $this->getUser()->getCashBacksByUserId($page);

        $tmp = "";

        foreach ($cashbacks as $key => $cash)
            $tmp .= sprintf("Заведение *%s* _%s_ чек №%s принес вам *%s* руб. CashBack\n",
                $cash->company->title,
                $cash->created_at,
                $cash->check_info,
                round(intval($cash->money_in_check) * env("CAHSBAK_PROCENT") / 100)
            );

        $this->reply(strlen($tmp) > 0 ? $tmp : "Вам не начислялся CashBack.");
        $this->pagination("/cashbacks", $cashbacks, $page, "Ващи дальнейшие действия");
    }

    public function getAchievements($page, $isAll = true)
    {
        if ($isAll)
            $achievements = Achievement::where("is_active", 1)
                    ->skip($page * config("bot.results_per_page"))
                    ->take(config("bot.results_per_page"))
                    ->orderBy('position', 'ASC')
                    ->get() ?? null;
        else
            $achievements = $this->getUser()->getAchievements($page);

        if (count($achievements) == 0 || $achievements == null) {
            $this->reply("Достижения будут доступны в скором времени!");
            return;
        }

        foreach ($achievements as $key => $achievement) {

            $keyboard = [
                [
                    ["text" => "Подробнее", "callback_data" => "/achievements_description " . $achievement->id]
                ]
            ];
            $message = sprintf("%s *%s*\n_%s_",
                ($achievement->activated == 0 ? "" : "\xE2\x9C\x85"),
                ($achievement->title ?? "Без названия [#" . $achievement->id . "]"),
                $achievement->description
            );
            $this->sendPhoto($message, $achievement->ach_image_url, $keyboard);
        }


        $this->pagination($isAll ? "/achievements_all" : "/achievements_my", $achievements, $page, "Выберите действие");

    }

    public function getAchievementsAll($page)
    {
        $this->getAchievements($page, true);
    }

    public function getAchievementsMy($page)
    {
        $this->getAchievements($page, false);
    }

    public function getAchievementsInfo($id)
    {

        $achievement = Achievement::find($id);

        $stat = $this->getUser(["stats"])
                ->stats()
                ->where("stat_type", "=", $achievement->trigger_type->value)
                ->first() ?? null;

        if ($achievement == null) {
            $this->reply("Достижение не найдено!");
            return;
        }


        $currentVal = ($stat == null ? 0 : $stat->stat_value);

        $progress = ($currentVal >= $achievement->trigger_value ?
            "\n*Успешно выполнено!*" :
            "Прогресс:*" . $currentVal . "* из *" . $achievement->trigger_value . "*");

        $message = sprintf("*%s*\n_%s_\n%s",
            $achievement->title,
            $achievement->description,
            $progress
        );

        $this->sendPhoto($message, $achievement->ach_image_url);

        $message = sprintf("*\xF0\x9F\x91\x86Ваш приз:*\n_%s_",
            $achievement->prize_description
        );

        $this->sendPhoto($message, $achievement->prize_image_url);


        $on_ach_activated = $this->getUser(["achievements"])
            ->achievements()
            ->where("achievement_id", $id)
            ->first();


        $keyboard = [];

        if ($on_ach_activated)
            if ($on_ach_activated->activated == false)
                array_push($keyboard, [
                    ["text" => "\xF0\x9F\x8E\x81Получить приз", "callback_data" => "/achievements_get_prize $id"]
                ]);

        array_push($keyboard, [
            ["text" => "\xE2\x8F\xAAВернуться назад", "callback_data" => "/achievements_panel"]
        ]);

        $this->sendMessage("Дальнейшие действие", $keyboard);
    }

    public function getAchievementsPrize($id)
    {
        $achievement = $this->getUser(["achievements"])
            ->achievements()
            ->where("achievement_id", $id)
            ->first();


        if ($achievement->activated == true) {
            $this->reply("Вы уже получили приз за данное достижение!");
            return;
        }

        $stat = $this->getUser(["stats"])
                ->stats()
                ->where("stat_type", "=", $achievement->trigger_type->value)
                ->first() ?? null;

        $currentVal = $stat == null ? 0 : $stat->stat_value;

        if ($currentVal <= $achievement->trigger_value) {
            $this->reply("Вы не можете получить приз за данное достижение");
            return;
        }

        $tmp_id = (string)$this->getChatId();
        while (strlen($tmp_id) < 10)
            $tmp_id = "0" . $tmp_id;

        $tmp_achievement_id = (string)$achievement->id;
        while (strlen($tmp_achievement_id) < 10)
            $tmp_achievement_id = "0" . $tmp_achievement_id;

        $code = base64_encode("012" . $tmp_id . $tmp_achievement_id);

        $qr_url = env("QR_URL") . "https://t.me/" . env("APP_BOT_NAME") . "?start=$code";

        $this->sendPhoto('_Код для активации достижения_', $qr_url);

    }

    public function getRefLink($id)
    {

        if (!$this->getUser()->hasPhone()) {
            $keyboard = [
                [
                    ["text" => "Заполнить", "callback_data" => "/fillinfo"]
                ]
            ];

            $this->sendMessage("У вас не заполнена личная информация! Заполняй и делись ссылкой:)", $keyboard);
            return;
        }

        $tmp_id = (string)$this->getChatId();
        while (strlen($tmp_id) < 10)
            $tmp_id = "0" . $tmp_id;

        switch ($id) {
            default:
            case 1:
                $comand_code = "001";
                break;
            case 2:
                $comand_code = "004";
                break;
            case 3:
                $comand_code = "005";
                break;
            case 4:
                $comand_code = "006";
                break;
        }

        $code = base64_encode($comand_code . $tmp_id . "0000000000");
        $url_link = "https://t.me/" . env("APP_BOT_NAME") . "?start=$code";
        $href_url_link = "<a href='" . $url_link . "'>Пересылай сообщение друзьям и получай больше баллов!</a>";
        $this->reply("Делись ссылкой с друзьями:\n" . ($id == 1 ? $href_url_link : $url_link));

    }

    public function getFAQMenu()
    {

        $keyboard = [
            [
                ['text' => "Навигация по боту", 'callback_data' => "/help"],
                ['text' => "Для потребителей", 'callback_data' => "/for_users"],
            ],
            [
                ['text' => "Реализация бонусов", 'callback_data' => "/rules"],
            ],
            [
                ['text' => "Промоутеру", 'callback_data' => "/promouter"],
                ['text' => "Поставщикам", 'callback_data' => "/suppliers"],
            ],
            [
                ['text' => "О нас", 'callback_data' => "/about"],
                ['text' => "Разработчики", 'callback_data' => "/dev"],
            ],
            [
                ['text' => 'Розыгрыш', 'callback_data' => '/start_lottery_test']
            ]


        ];

        $this->sendMessage("*F.A.Q.*\n_Не знаете как начать пользоваться? - Почитайте наше описание! Узнайте больше о приложении, компании и разработчике!_", $keyboard);

    }

    public function getFAQSimpleMenu()
    {

        $keyboard = [
            [
                ['text' => "Полезная информация", 'callback_data' => "/articles 0"],

            ],
            [
                ['text' => "Как пользоваться", 'callback_data' => env('APP_URL') . "/faq"],
            ],
        ];

        $this->sendMessage("*F.A.Q.*\n_Не знаете как начать пользоваться? - Почитайте наше описание! Узнайте больше о приложении, компании и разработчике!_", $keyboard);

    }

    public function getPromotionsMenu()
    {
        $keyboard = [
            [
                ['text' => "\xF0\x9F\x92\x8EПо категориям", 'callback_data' => '/promo_by_category 0'],
                ['text' => "\xF0\x9F\x8F\xA6По компаниям", 'callback_data' => '/promo_by_company 0'],
            ],
            [
                ['text' => "\xE2\xAD\x90Достижения", 'callback_data' => "/achievements_panel"],
            ],
            [
                ['text' => "\xE2\x9A\xA1Акции и призы на сайте", 'url' => env("APP_PROMO_LINK")],
            ],
            [
                ['text' => "\xF0\x9F\x8C\xBBБольше информации в нашем канале", 'url' => env("CHANNEL_LINK")],
            ],

        ];

        $this->sendMessage("Самые свежие акции", $keyboard);
    }

    public function getFriends($page)
    {

        $refs = $this->getUser()->getFriends($page);

        $sender = $this->getUser(["parent"])
            ->parent;

        $tmp = "";

        if ($sender != null) {

            $userSenderName = $sender->fio_from_telegram ??
                $sender->fio_from_request ??
                $sender->telegram_chat_id ??
                'Неизвестный пользователь';

            $tmp = "Вас пригласил - \xF0\x9F\x91\x91*$userSenderName*\n";

        }

        if ($refs != null)
            foreach ($refs as $key => $ref) {
                if ($ref->recipient != null) {
                    $userName = $ref->recipient->fio_from_telegram ??
                        $ref->recipient->fio_from_request ??
                        $ref->recipient->telegram_chat_id ??
                        'Неизвестный пользователь';
                    $tmp .= ($key + 1) . ". " . $userName . ($ref->activated == 0 ? "\xE2\x9D\x8E" : "\xE2\x9C\x85") . "\n";
                }
            }

        $this->reply(strlen($tmp) > 0 ? $tmp : "У вас нет друзей:(");
        $this->pagination('/friends', $refs, $page, "Ваши действия");
    }

    public function getPaymentMenu()
    {
        // TODO: Implement getPaymentMenu() method.
    }

    public function getStatisticMenu()
    {
        $keyboard = [
            [
                ["text" => "Начисления", "callback_data" => "/cashbacks 0"],
                ["text" => "Списания", "callback_data" => "/payments 0"],
            ],
            /*  [
                  ["text" => "Благотворительность", "callback_data" => "/charity"]
              ]*/
        ];
        $this->sendMessage("Вы можете отслеживать начисления CashBack бонусов и их списание!", $keyboard);
    }

    public function getAchievementsMenu()
    {
        $keyboard = [
            [
                ["text" => "\xF0\x9F\x8D\x80Все достижения", "callback_data" => "/achievements_all 0"],
                ["text" => "\xE2\xAD\x90Мои достижения", "callback_data" => "/achievements_my 0"],
            ]
        ];
        $this->sendMessage("Получайте достижения и обменивайте их на крутейшие призы!", $keyboard);
    }

    public function getPromouterMenu()
    {
        // TODO: Implement getPromouterMenu() method.
    }

    public function getPromotionsByCompany($page)
    {
        $companies = Company::where("is_active", true)
            ->orderBy('position', 'DESC')
            ->take(config("bot.results_per_page"))
            ->skip($page * config("bot.results_per_page"))
            ->get();

        if (count($companies) == 0) {
            $this->reply("К сожалению, нет добавленных компаний:(");
            return;
        }

        foreach ($companies as $company) {
            $keyboard = [
                [
                    ["text" => "Посмотреть акции", "callback_data" => "/company " . $company->id . " 0"]
                ]
            ];

            if (!is_null($company->menu_url)) {
                array_push($keyboard, [
                    ["text" => "\xE2\x9D\x97\xE2\x9D\x97\xE2\x9D\x97Акционное меню\xE2\x9D\x97\xE2\x9D\x97\xE2\x9D\x97", "url" => $company->menu_url]
                ]);
            }

            $this->sendPhoto('*' . $company->title . "*\n", $company->logo_url, $keyboard);
        }

        $this->pagination("/promo_by_company", $companies, $page, "Выберите действие");

    }

    public function getPromotionsByCategory($page)
    {

        $categories = Category::orderBy('position', 'DESC')
            ->take(config("bot.results_per_page"))
            ->skip($page * config("bot.results_per_page"))
            ->get();

        if (count($categories) == 0) {
            $this->reply("К сожалению, нет добавленных категорий:(");
            return;
        }

        foreach ($categories as $cat) {

            $keyboard = [
                [
                    ["text" => "Посмотреть акции", "callback_data" => "/category " . $cat->id . " 0"]
                ]
            ];

            $this->sendPhoto("*$cat->title*", $cat->image_url, $keyboard);
        }

        $this->pagination("/promo_by_category", $categories, $page, "Выберите действие");
    }

    public function getCategoryById($id, $page)
    {

        $promotions = (Category::with(["promotions", "promotions.company"])
            ->where("id", $id)
            ->first())
            ->promotions()
            ->orderBy('position', 'DESC')
            ->take(config("bot.results_per_page"))
            ->skip($page * config("bot.results_per_page"))
            ->get();

        $isEmpty = true;
        foreach ($promotions as $promo) {

            $company = Company::find($promo->company_id);

            $on_promo = $promo->onPromo($this->getChatId());
            $isActive = $promo->isActive();

            if (!$on_promo && $isActive && $company->is_active) {

                $isEmpty = false;

                $keyboard = [
                    [
                        ["text" => "\xF0\x9F\x91\x89Подробнее", 'callback_data' => $promo->handler == null ? "/promotion " . $promo->id : $promo->handler . " " . $promo->id]
                    ]
                ];

                $this->sendPhoto("*" . $promo->title . "*", $promo->promo_image_url, $keyboard);
            }
        }

        if ($isEmpty)
            $this->reply("Акций в категории не найдено или все акции собраны:(");

        $this->pagination("/category $id", $promotions, $page, "Выберите действие");
    }

    public function getCompanyById($id, $page)
    {

        $company = \App\Company::with(["promotions", "promotions.users"])
            ->where("id", $id)
            ->orderBy('position', 'DESC')
            ->first();

        if (!$company->is_active) {
            $this->reply("Акции этой компании временно недоступны!");
            return;
        }

        $keyboard = [];

        if (strlen(trim($company->telegram_bot_url)) > 0)
            array_push($keyboard, [
                ['text' => "\xF0\x9F\x91\x89Перейти в бота", 'url' => $company->telegram_bot_url],
            ]);

        $message = sprintf("%s\n_%s_",
            $company->title,
            $company->description
        );

        $this->sendPhoto($message, $company->logo_url, $keyboard);

        $promotions = $company->promotions()
            ->orderBy('position', 'DESC')
            ->take(config("bot.results_per_page"))
            ->skip($page * config("bot.results_per_page"))
            ->get();

        $isEmpty = true;

        foreach ($promotions as $promo) {

            $on_promo = $promo->onPromo($this->getChatId());
            $isActive = $promo->isActive();


            if (!$on_promo && $isActive) {

                $isEmpty = false;

                $keyboard = [
                    [
                        ['text' => "\xF0\x9F\x91\x89Подробнее", 'callback_data' => $promo->handler == null ? "/promotion " . $promo->id : $promo->handler . " " . $promo->id],
                    ],
                ];

                $this->sendPhoto("*" . $promo->title . "*", $promo->promo_image_url, $keyboard);

            }
        }

        if ($isEmpty)
            $this->reply("Акций у этой компании не найдено или все акции уже прошли:(");

        $this->pagination("/company $id", $promotions, $page, "Выберите действие");
    }

    public function getArticlesByPartId($partId, $page = 0)
    {
        $articles = Article::where("part", $partId)
            ->where("is_visible", 1)
            ->skip($page * config("bot.results_per_page"))
            ->take(config("bot.results_per_page"))
            ->orderBy('id', 'DESC')
            ->get();

        if (count($articles) > 0) {
            foreach ($articles as $article)
                $this->reply($article->url);
        } else
            $this->reply("Статьи появятся в скором времени!");

        $this->pagination("/articles $partId", $articles, $page, "Ваши действия");
    }

    public function getLotterySlot($slotId, $codeId)
    {
        $code = Promocode::find($codeId);
        if ($code == null) {
            $this->reply("Увы, что-то пошло не так и код более не действителен:(");
            return;
        }
        if ($code->prize_id != null) {
            $this->reply("Приз по данному коду уже был выбран ранее!");
            return;
        }
        $prize = Prize::with(["company"])
                ->where("id", $slotId)
                ->first() ?? null;

        if ($prize == null) {
            $this->reply("Увы, что-то пошло не так и приза нет:(");
            return;
        }
        if ($prize->current_activation_count == $prize->summary_activation_count) {
            $this->reply("Увы, к данному моменту все призы закончились");
            return;
        }

        $message = "*" . $prize->title . "*\n"
            . "_" . $prize->description . "_\n";
        $prize->current_activation_count++;
        $prize->updated_at = Carbon::now();
        $prize->save();

        $code->prize_id = $prize->id;
        $code->updated_at = Carbon::now();
        $code->save();

        $this->sendPhoto($message, $prize->image_url);

        $companyTitle = $prize->company->title;
        $message = "*Пользователь поучаствовал в розыгрыше от компании $companyTitle и выиграл:*\n$message"
            . "*Дата участия*:" . (Carbon::now()) . "\n";

        $this->sendPhotoToChanel($message, $prize->image_url);
    }

    public function getActivityInformation()
    {
        $stat_types = [
            "\xE2\x96\xAAКоличество активаций приза по акции: *%d* раз.\n",
            "\xE2\x96\xAAКоличество рефералов:  *%d* человек.\n",
            "\xE2\x96\xAAМаксимальное количество накопленного CashBack: *%d* руб.\n",
            "\xE2\x96\xAAКоличество переходов из ВК: *%d* раз.\n",
            "\xE2\x96\xAAКоличество переходов из Facebook: *%d* раз.\n",
            "\xE2\x96\xAAКоличество переходов из Instagram: *%d* раз.\n",
            "\xE2\x96\xAAКоличество переходов из других источников: *%d* раз.\n",
            "\xE2\x96\xAAМасимальный реферальный бонус: *%d* руб.\n",
            "\xE2\x96\xAAКоличество активированных достижений: *%d* ед.\n",
            "\xE2\x96\xAAМаксимальное количество списанного CashBack: *%d* руб.\n",
        ];

        $stats = $this->getUser()->getStats();

        $message = "";

        foreach ($stats as $stat)
            $message .= sprintf($stat_types[$stat->stat_type->value], $stat->stat_value);

        $this->reply(count($stats) > 0 ? $message : "Статистика еще не ведется для вашего аккаунта!");
    }

    public function getMyFriends()
    {

        $ref = $this->getUser()->referrals_count;

        $network = $this->getUser()->network_friends_count + $ref;

        $network_tmp = $this->getUser()->current_network_level > 0 ? "Сеть друзей *$network* чел.!\n" : "";

        $message = "Вы пригласили *$ref* друзей!\n" . $network_tmp . "_Делитесь Вашим QR-кодом и накапливайте баллы!_\n";

        $keyboard = [

            [
                ["text" => "Пригласить друзей", "switch_inline_query" => ""]
            ],
            [

                ["text" => "Посмотреть друзей", "callback_data" => "/friends 0"]
            ]
        ];

        /*        if ($ref > 0)
                    $this->sendMessage($message, $keyboard);
                else
                    $this->reply($message);*/


        $tmp_id = (string)$this->getChatId();
        while (strlen($tmp_id) < 10)
            $tmp_id = "0" . $tmp_id;

        $code = base64_encode("001" . $tmp_id . "0000000000");

        $qr_url = env("QR_URL") . "https://t.me/" . env("APP_BOT_NAME") . "?start=$code";

        $this->sendPhoto("_Ваш реферальный код_\n$message", $qr_url, ($ref > 0 ? $keyboard : []));

    }

    public function getMyMoney()
    {

        $summary = $this->getUser()->referral_bonus_count +
            $this->getUser()->cashback_bonus_count +
            $this->getUser()->network_cashback_bonus_count;

        $cashback = $this->getUser()->cashback_bonus_count;

        $tmp_network = $this->getUser()->network_friends_count >= config("bot.step_one_friends") ?
            "Сетевой бонус *" . $this->getUser()->network_cashback_bonus_count . "*\n" : '';

        $message = "У вас *$summary* баллов, из них *$cashback* - бонус CashBack!\n" .
            $tmp_network . "_Для оплаты дайте отсканировать данный QR-код сотруднику!_\n";


        $keyboard = [
            [
                ["text" => "Мой бюджет", "callback_data" => "/statistic"]
            ]
        ];


        $cashback_history = $this->getUser()->getCashBacksByPhone(0);

        if (count($cashback_history) > 0) {
            $tmp_money = 0;
            foreach ($cashback_history as $ch)
                if ($ch->activated == 0)
                    $tmp_money += round(intval($ch->money_in_check) * env("CAHSBAK_PROCENT") / 100);

            if ($tmp_money > 0)
                array_push($keyboard, [
                    ["text" => "Зачислить мне $tmp_money руб. CashBack", "callback_data" => "/cashback_get"]
                ]);

        }

        //$this->sendMessage($message,$keyboard);

        $tmp_id = (string)$this->getChatId();
        while (strlen($tmp_id) < 10)
            $tmp_id = "0" . $tmp_id;

        $code = base64_encode("002" . $tmp_id . "0000000000");

        $qr_url = env("QR_URL") . "https://t.me/" . env("APP_BOT_NAME") . "?start=$code";

        $this->sendPhoto("_Ваш код для оплаты_\n$message", $qr_url, $keyboard);
    }

    public function getLotteryMenu()
    {

        $rules = Article::where("part", Parts::Lottery)
            ->where("is_visible", 1)
            ->orderBy("id", "DESC")
            ->first();

        $keyboard = [
            [
                ['text' => "Ввести код и начать", 'callback_data' => "/lottery"]
            ]
        ];

        if ($rules != null)
            array_push($keyboard, [['text' => "Условия розыгрыша и призы", 'url' => $rules->url]]);

        $this->sendMessage("Розыгрыш призов", $keyboard);
    }

    public function getLatestCashBack()
    {
        if ($this->getUser()->hasPhone())
            return;

        $cashback_history = $this->getUser()->getLatestCashBack();

        if (count($cashback_history) > 0) {
            foreach ($cashback_history as $ch) {
                $ch->activated = 1;
                $ch->user_id = $this->getUser()->id;
                $ch->save();

                $user = $this->getUser();
                $user->cashback_bonus_count += round(intval($ch->money_in_check) * env("CAHSBAK_PROCENT") / 100);
                $user->save();

            }
            $this->reply("CashBack успешно зачислен!");
        }

    }

    public function getFallback()
    {
        $this->bot->loadDriver(TelegramInlineQueryDriver::DRIVER_NAME);

        $queryObject = json_decode($this->bot->getDriver()->getEvent());

        if ($queryObject) {

            $id = $queryObject->from->id;

            $query = $queryObject->query;


            $button_list = [];

            if (strlen(trim($query)) > 0) {
                $promotions =
                    Promotion::where("title", "like", "%$query%")
                        ->orWhere("description", "like", "%$query%")
                        ->take(5)
                        ->skip(0)
                        ->orderBy("id", "DESC")
                        ->get();

                foreach ($promotions as $promo) {
                    $isActive = $promo->isActive();
                    if ($isActive) {

                        $tmp_id = (string)$id;
                        while (strlen($tmp_id) < 10)
                            $tmp_id = "0" . $tmp_id;

                        $tmp_promo_id = (string)$promo->id;
                        while (strlen($tmp_promo_id) < 10)
                            $tmp_promo_id = "0" . $tmp_promo_id;

                        $code = base64_encode("001" . $tmp_id . $tmp_promo_id);
                        $url_link = "https://t.me/" . env("APP_BOT_NAME") . "?start=$code";

                        $tmp_button = [
                            'type' => 'article',
                            'id' => uniqid(),
                            'title' => $promo->title,
                            'input_message_content' => [
                                'message_text' => $promo->description . "\n" . $promo->promo_image_url,
                            ],
                            'reply_markup' => [
                                'inline_keyboard' => [
                                    [
                                        ['text' => "\xF0\x9F\x91\x89Перейти к акции", "url" => "$url_link"],
                                    ],

                                ]
                            ],
                            'thumb_url' => $promo->promo_image_url,
                            'url' => env("APP_URL"),
                            'description' => $promo->description,
                            'hide_url' => true
                        ];

                        array_push($button_list, $tmp_button);


                    }
                }
            } else {

                $tmp_id = (string)$id;
                while (strlen($tmp_id) < 10)
                    $tmp_id = "0" . $tmp_id;

                $code = base64_encode("001" . $tmp_id . "0000000000");
                $url_link = "https://t.me/" . env("APP_BOT_NAME") . "?start=$code";

                $tmp_button = [
                    'type' => 'article',
                    'id' => uniqid(),
                    'title' => "Приглашаем Вас воспользоваться услугами нашего сервиса!",
                    'input_message_content' => [
                        'message_text' => "
                        Добрый день!🙋‍♂️
Мы рады, что Вы вместе с нами😃.
Лучшие скидки💥, акции💯 и мероприятии🔥 нашего любимого города🥰 уже ждут встречи с Вами✨

📌 https://t.me/skidkidn - подписывайтесь на наши обновления и мы подготовим Вам интересные конкурсы и розыгрыши призов🎁.

!!!Новые скидки каждую неделю!!!

📍 Скидочные предложения и Cashback не суммируется с внутренними акциями и скидочными предложениями на товары и услуги наших Партнеров.\nhttps://sun9-35.userapi.com/c205328/v205328682/56913/w8tBXIcG91E.jpg
                        ",
                    ],
                    'reply_markup' => [
                        'inline_keyboard' => [
                            [
                                ['text' => "\xF0\x9F\x91\x89Перейти к акции", "url" => "$url_link"],
                            ],

                        ]
                    ],
                    'thumb_url' => "https://sun9-35.userapi.com/c205328/v205328682/56913/w8tBXIcG91E.jpg",
                    'url' => env("APP_URL"),
                    'description' => "Переходи по ссылке и получай крутые акции и скидки города!!",
                    'hide_url' => true
                ];

                array_push($button_list, $tmp_button);
            }
            return $this->bot->sendRequest("answerInlineQuery",
                [
                    'cache_time' => 0,
                    "inline_query_id" => json_decode($this->bot->getEvent())->id,
                    "results" => json_encode($button_list)
                ]);
        }

    }
}
