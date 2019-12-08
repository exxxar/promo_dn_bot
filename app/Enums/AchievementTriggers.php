<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class AchievementTriggers extends Enum
{
    const PromosActivationSequence = 0;
    const ReferralCount =   1;
    const MaxCashBackCount = 2;
    const VKLinksActivationCount = 3;
    const FBLinksActivationCount = 4;
    const InstaLinksActivationCount = 5;
    const QRActivationCount = 6;
    const MaxReferralBonusCount = 7;
    const AchievementActivatedCount = 8;
    const MaxCashBackRemoveBonus = 9;
}
