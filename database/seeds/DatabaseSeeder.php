<?php

use App\Enums\AchievementTriggers;
use App\Events\AchievementEvent;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PrizesTableSeeder::class);

      /*  $users = \App\User::all();
        foreach ($users as $u)
            event(new AchievementEvent(AchievementTriggers::MaxReferralBonusCount, 10, $u));*/
    }
}
