<?php

use App\Drivers\CustomTelegramDriver;
use App\Http\Controllers\BotController;
use App\Http\Controllers\BotManController;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;


$botman = resolve('botman');


$botman->hears(".*Ввести код и начать|/lottery", BotController::class . '@lotteryConversation');
$botman->hears(".*Акции в Instagram|/insta_promos ([0-9]+)", BotController::class . '@getInstaPromosList');

$botman->hears(".*Участвовать за CashBack", BotController::class . '@getLotteryCashBackCompanies');
$botman->hears(".*Промокод в подарок", BotController::class . '@getLotteryGiftCompanies');

$botman->hears("/pay_lottery (gift|lottery) ([0-9]+)", BotController::class . '@payForLottery');

$botman->hears("/start ([0-9a-zA-Z=]+)", BotController::class . '@startDataConversation');
$botman->hears("/promotion ([0-9]+)", BotController::class . '@promoConversation');
$botman->hears("/lotusprofile ([0-9]+)", BotController::class . '@lotusprofileConversation');
$botman->hears("/fillinfo", BotController::class . '@fillInfoConversation');
$botman->hears("/fill_insta_info", BotController::class . '@fillInstagramConversation');
$botman->hears("/payment ([0-9]{1,10}) ([0-9]{1,10})", BotController::class . '@paymentConversation');
$botman->hears(".*Поделиться в соц. сетях|/ref ([0-9]+)", BotController::class . '@getRefs');
$botman->hears('.*Посмотреть моих друзей|/friends ([0-9]+)', BotController::class . '@getFriends');

$botman->hears('.*Благотворительность|/charity ([0-9]+)', BotController::class . '@getCharityList');
$botman->hears('/get_charity_item ([0-9]+)', BotController::class . '@getCharity');
$botman->hears('/donate ([0-9]+) ([0-9]{2,5})', BotController::class . '@donateCharity');

$botman->hears('/category ([0-9]+) ([0-9]+)', BotController::class . '@getCategoryById');
$botman->hears('/company ([0-9]+) ([0-9]+)', BotController::class . '@getCompanyById');
$botman->hears('.*Мои достижения|/achievements_my ([0-9]+)', BotController::class . '@getAchievementsMy');
$botman->hears('/achievements_description ([0-9]+)', BotController::class . '@getAchievementDescriptionById');
$botman->hears('/achievements_get_prize ([0-9]+)', BotController::class . '@getAchievementPrizeById');
$botman->hears('/check_lottery_slot ([0-9]+) ([0-9]+)', BotController::class . '@getLotterySlot');
$botman->hears('/cashback_get', BotController::class . '@getLatestCashBack'); //перенёс
$botman->hears('.*Розыгрыш|/start_lottery_test', BotController::class . '@getLotteryMenu'); //перенёс
$botman->hears('.*Все достижения|/achievements_all ([0-9]+)', BotController::class . '@getAchievementsAll'); //перенёс
$botman->hears('.*Система достижений|/achievements_panel', BotController::class . '@getAchievementMenu'); //перенёс
$botman->hears('.*Статистика активности|/activity_information', BotController::class . '@getActivityInformation');
$botman->hears('.*CashBack по компаниям|/get_cashback_by_companies', BotController::class . '@getCashBackByCompanies')->stopsConversation();

$botman->hears(".*Пригласить друзей|".__("messages.global_menu_1"), BotController::class . '@getFriendsMenu');

$botman->hears(".*Оплатить бонусами|".__("messages.global_menu_2"), BotController::class . '@getPaymentMenu');
$botman->hears(".*Наши мероприятия", BotController::class . '@getEventsMenu');
$botman->hears(".*Помощь|".__("messages.global_menu_3"), BotController::class . '@getFAQMenu');
$botman->hears(".*Акции, скидки и мероприятия|".__("messages.global_menu_5"), BotController::class . '@getPromotionMenu');

$botman->hears('.*Акции по компаниям|/promo_by_company ([0-9]+)', BotController::class . '@getPromoByCompanies');
$botman->hears('.*Акции по категориям|/promo_by_category ([0-9]+)', BotController::class . '@getPromoByCategories');
$botman->hears('.*Списания CashBack|/payments ([0-9]+)', BotController::class . '@getPayments');
$botman->hears('.*Начисления CashBack|/cashbacks ([0-9]+)', BotController::class . '@getCashbacks');
$botman->hears('/events ([0-9]+)', BotController::class . '@getEvents');
$botman->hears('.*Полезная информация|/articles ([0-9]+)', BotController::class . '@getArticlesByPage');
$botman->hears('.*Как пользоваться|/help', BotController::class . '@getHelp');
$botman->hears('.*Соглашение на обработку данных|/rules', BotController::class . '@getRules');
$botman->hears('/for_users', BotController::class . '@getForUsers');
$botman->hears('/dev', BotController::class . '@getDev');
$botman->hears('/about', BotController::class . '@getAbout');
$botman->hears('/statistic', BotController::class . '@getStatistic');
$botman->hears('/promouter', BotController::class . '@getPromouterInfo');
$botman->hears('/suppliers', BotController::class . '@getSuppliers');

$botman->hears('Продолжить позже|stop', BotManController::class . '@stopConversation')->stopsConversation();
$botman->hears("Попробовать снова|/start", BotController::class . '@startConversation');
$botman->hears(".*Главное меню", BotController::class . '@getMainMenu');

$botman->fallback(BotController::class . "@fallback");

$botman->receivesImages(BotController::class."@uploadImages");
$botman->receivesLocation(BotController::class."@receivesLocations");

$botman->hears('/promo_edit_data', BotController::class . "@doPromotionEditData");
