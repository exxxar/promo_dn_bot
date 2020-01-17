<?php


namespace App\Classes;


interface iSkidkiDNBot
{
    public function getEventsAll($page);
    public function getFriendsAll($page);
    public function getPaymentsAll($page);
    public function getCashBacksAll($page);

    public function getAchievementsAll($page);
    public function getAchievementsMy($page);
    public function getAchievementsInfo($id);
    public function getAchievementsPrize($id);


    public function getMyFriends($page);
    public function getMyMoney();

    public function getRefLink();

    public function getFAQMenu();
    public function getPromotionsMenu();
    public function getFriends($page);
    public function getPaymentMenu();
    public function getStatisticMenu();
    public function getAchievementsMenu();
    public function getPromouterMenu();

    public function getLotteryMenu();

    public function getLatestCashBack();


    public function getPromotionsByCategory($page);
    public function getPromotionsByCompany($page);

    public function getCategoryById($id);
    public function getCompanyById($id);

    public function getArticlesByPartId($partId);

    public function getActivityInformation();

    public function getLotterySlot($slotId,$codeId);

}
