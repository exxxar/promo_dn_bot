<?php
use App\Http\Controllers\BotController;
use App\Http\Controllers\BotManController;

$botman = resolve('botman');


$botman->hears("/lottery", BotController::class . '@lotteryConversation');
$botman->hears("/insta_promos ([0-9]+)", BotController::class . '@getInstaPromosList');
$botman->hears("/start_cashback_lottery", BotController::class . '@lotteryCashback');
$botman->hears("/get_gift_companies (gift|lottery)", BotController::class . '@getLotteryGiftCompanies');
$botman->hears("/pay_lottery (gift|lottery) ([0-9]+)", BotController::class . '@payForLottery');

$botman->hears("/start ([0-9a-zA-Z=]+)", BotController::class . '@startDataConversation');
$botman->hears("/promotion ([0-9]+)", BotController::class . '@promoConversation');
$botman->hears("/lotusprofile ([0-9]+)", BotController::class . '@lotusprofileConversation');
$botman->hears("/fillinfo", BotController::class . '@fillInfoConversation');
$botman->hears("/payment ([0-9]{1,10}) ([0-9]{1,10})", BotController::class . '@paymentConversation');
$botman->hears("/ref ([0-9]+)", BotController::class . '@getRefs');
$botman->hears('/friends ([0-9]+)', BotController::class . '@getFriends');
$botman->hears('/category ([0-9]+) ([0-9]+)', BotController::class . '@getCategoryById');
$botman->hears('/company ([0-9]+) ([0-9]+)', BotController::class . '@getCompanyById');
$botman->hears('/achievements_my ([0-9]+)', BotController::class . '@getAchievementsMy');
$botman->hears('/achievements_description ([0-9]+)', BotController::class . '@getAchievementDescriptionById');
$botman->hears('/achievements_get_prize ([0-9]+)', BotController::class . '@getAchievementPrizeById');
$botman->hears('/check_lottery_slot ([0-9]+) ([0-9]+)', BotController::class . '@getLotterySlot');
$botman->hears('/cashback_get', BotController::class . '@getLatestCashBack'); //перенёс
$botman->hears('.*Розыгрыш|/start_lottery_test', BotController::class . '@getLotteryMenu'); //перенёс
$botman->hears('/achievements_all ([0-9]+)', BotController::class . '@getAchievementsAll'); //перенёс
$botman->hears('/achievements_panel', BotController::class . '@getAchievementMenu'); //перенёс
$botman->hears('/activity_information', BotController::class . '@getActivityInformation');
$botman->hears(__("messages.global_menu_1"), BotController::class . '@getMyFriends');
$botman->hears(__("messages.global_menu_2"), BotController::class . '@getMyMoney');
$botman->hears(__("messages.global_menu_3"), BotController::class . '@getEventsMenu');
$botman->hears(__("messages.global_menu_4"), BotController::class . '@getFAQMenu');
$botman->hears(__("messages.global_menu_5"), BotController::class . '@getPromotionMenu');
$botman->hears('/promo_by_company ([0-9]+)', BotController::class . '@getPromoByCompanies');
$botman->hears('/promo_by_category ([0-9]+)', BotController::class . '@getPromoByCategories');
$botman->hears('/payments ([0-9]+)', BotController::class . '@getPayments');
$botman->hears('/cashbacks ([0-9]+)', BotController::class . '@getCashbacks');
$botman->hears('/events ([0-9]+)', BotController::class . '@getEvents');
$botman->hears('Полезная информация|/articles ([0-9]+)', BotController::class . '@getArticlesByPage');
$botman->hears('Как пользоваться|/help', BotController::class . '@getHelp');
$botman->hears('Соглашение на обработку данных|/rules', BotController::class . '@getRules');
$botman->hears('/for_users', BotController::class . '@getForUsers');
$botman->hears('/dev', BotController::class . '@getDev');
$botman->hears('/about', BotController::class . '@getAbout');
$botman->hears('/statistic', BotController::class . '@getStatistic');
$botman->hears('/promouter', BotController::class . '@getPromouterInfo');
$botman->hears('/suppliers', BotController::class . '@getSuppliers');

$botman->hears('Продолжить позже|stop', BotManController::class . '@stopConversation')->stopsConversation();
$botman->hears("Главное меню|Попробовать снова|/start", BotController::class . '@startConversation');

$botman->fallback(BotController::class . "@fallback");

