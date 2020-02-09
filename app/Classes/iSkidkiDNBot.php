<?php


namespace App\Classes;


interface iSkidkiDNBot
{
    public function getEventsAll($page);
    public function getPaymentsAll($page);
    public function getCashBacksAll($page);

    public function getAchievements($page, $isAll = true);
    public function getAchievementsAll($page);
    public function getAchievementsMy($page);
    public function getAchievementsInfo($id);
    public function getAchievementsPrize($id);


    public function getMyFriends();
    public function getMyMoney();

    public function getRefLink($id);

    public function getFAQMenu();
    public function getFAQSimpleMenu();
    public function getFAQBottomMenu();

    public function getPromotionsMenu();
    public function getFriends($page);
    public function getPaymentMenu();
    public function getStatisticMenu();
    public function getAchievementsMenu();
    public function getPromouterMenu();

    public function getLotteryMenu();
    public function getLotteryCashBack();
    public function getLotteryGiftCompanies($giftType);
    public function payForLottery($giftType,$companyId);

    public function getLatestCashBack();

    public function getInstaPromos($page);


    public function getPromotionsByCategory($page);
    public function getPromotionsByCompany($page);

    public function getCategoryById($id,$page);
    public function getCompanyById($id,$page);

    public function getArticlesByPartId($partId);

    public function getActivityInformation();

    public function getLotterySlot($slotId,$codeId);

    public function getFallback();

}
