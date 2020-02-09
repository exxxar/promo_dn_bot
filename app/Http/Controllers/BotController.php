<?php

namespace App\Http\Controllers;


use App\Classes\SkidkiDNBot;
use App\Conversations\FillInfoConversation;
use App\Conversations\LotteryCashbackConversation;
use App\Conversations\LotteryConversation;
use App\Conversations\LotusProfileConversation;
use App\Conversations\PaymentConversation;
use App\Conversations\PromoConversation;
use App\Conversations\StartConversation;
use App\Conversations\StartDataConversation;
use App\Conversations\StopConversation;
use App\Enums\Parts;
use BotMan\BotMan\Facades\BotMan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BotController extends Controller
{

    private $sdnbot;


    public function __construct(SkidkiDNBot $sdnbot)
    {
        $this->sdnbot = $sdnbot;
        $this->sdnbot->initKeyboards();
    }

    public function getLotterySlot($bot, $slotId, $codeId)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getLotterySlot($slotId, $codeId);

    }

    public function getForUsers($bot)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getArticlesByPartId(Parts::ForUsers, 0);
    }

    public function getSuppliers($bot)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getArticlesByPartId(Parts::Suppliers, 0);
    }

    public function getPromouterInfo($bot)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getArticlesByPartId(Parts::Promouter, 0);
    }

    public function getAbout($bot)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getArticlesByPartId(Parts::About_company, 0);
    }

    public function getDev($bot)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getArticlesByPartId(Parts::Developers, 0);
    }

    public function getRules($bot)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getArticlesByPartId(Parts::To_the_consumer, 0);
    }

    public function getHelp($bot)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getArticlesByPartId(Parts::How_to_use, 0);
    }

    public function getArticlesByPage($bot, $page=0)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getArticlesByPartId(Parts::Articles, $page);
    }

    /**
     * @deprecated устарело и больше не требуется
     */
    public function getStatistic($bot)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getStatisticMenu();
    }

    public function getEvents($bot, $page)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getEventsAll($page);
    }

    public function getCashbacks($bot, $page=0)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getCashBacksAll($page);
    }

    public function getPayments($bot, $page=0)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getPaymentsAll($page);
    }

    public function getPromoByCategories($bot, $page)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getPromotionsByCategory($page);
    }

    public function getPromoByCompanies($bot, $page)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getPromotionsByCompany($page);
    }

    public function getCategoryById($bot, $categoryId, $page)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getCategoryById($categoryId, $page);
    }

    public function getCompanyById($bot, $companyId, $page)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getCompanyById($companyId, $page);
    }

    public function getPromotionMenu($bot)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getPromotionsMenu();
    }

    public function getInstaPromosList($bot, $page)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getInstaPromos($page);
    }

    public function getActivityInformation($bot)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getActivityInformation();
    }

    public function getAchievementMenu($bot)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getAchievementsMenu();
    }

    public function getMainMenu($bot)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getMainMenu();
    }

    public function getFAQMenu($bot)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getFAQBottomMenu();
    }

    public function getEventsMenu($bot)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getEventsAll(0);
    }

    public function getMyMoney($bot)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getMyMoney();
    }

    public function getMyFriends($bot)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getMyFriends();
    }

    public function getLotteryGiftCompanies($bot, $giftType)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getLotteryGiftCompanies($giftType);
    }


    public function payForLottery($bot, $type, $companyId)
    {
        $this->sdnbot
            ->setBot($bot)
            ->payForLottery($type, $companyId);
    }

    public function getLotteryMenu($bot)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getLotteryMenu();
    }

    public function getLatestCashBack($bot)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getLatestCashBack();
    }

    public function getAchievementsAll($bot, $page)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getAchievementsAll($page);
    }

    public function getAchievementsMy($bot, $page)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getAchievementsMy($page);
    }


    public function getAchievementPrizeById($bot, $achId)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getAchievementsPrize($achId);
    }

    public function getAchievementDescriptionById($bot, $achId)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getAchievementsInfo($achId);
    }

    public function getFriends($bot, $page)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getFriends($page);
    }

    public function getRefs($bot, $id)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getRefLink($id);
    }

    public function paymentConversation($bot, $arg1, $arg2)
    {
        $this->sdnbot
            ->setBot($bot)
            ->startConversation(new PaymentConversation($bot, $arg1, $arg2));
    }

    public function fillInfoConversation($bot)
    {
        $this->sdnbot
            ->setBot($bot)
            ->startConversation(new FillInfoConversation($bot));
    }

    public function startConversation($bot)
    {
        $this->sdnbot
            ->setBot($bot)
            ->startConversation(new StartConversation($bot));
    }

    public function lotteryConversation($bot)
    {
        $this->sdnbot
            ->setBot($bot)
            ->startConversation(new LotteryConversation($bot));
    }

    public function lotteryCashback($bot)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getLotteryCashBack();
    }

    public function startDataConversation($bot, $data)
    {
        $this->sdnbot
            ->setBot($bot)
            ->startConversation(new StartDataConversation($bot, $data));
    }

    public function stopConversation($bot)
    {
        $this->sdnbot
            ->setBot($bot)
            ->startConversation(new StopConversation($bot));
    }

    public function promoConversation($bot, $data)
    {
        $this->sdnbot
            ->setBot($bot)
            ->startConversation(new PromoConversation($bot, $data));
    }

    public function lotusprofileConversation($bot, $data)
    {
        $this->sdnbot
            ->setBot($bot)
            ->startConversation(new LotusProfileConversation($bot, $data));
    }

    public function fallback($bot)
    {
        $this->sdnbot
            ->setBot($bot)
            ->getFallback();
    }
}
