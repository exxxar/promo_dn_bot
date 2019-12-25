<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        try {
            User::create([
                'name' => 'Aleks',
                'email' => "admin@gmail.com",
                'password' => bcrypt('adminsecret'),
                'fio_from_telegram' => "Алексей",
                'source' => '000',
                'telegram_chat_id' => "484698705",
                'referrals_count' => 0,
                'referral_bonus_count' => 10000,
                'cashback_bonus_count' => 10000,
                'is_admin' => true,
                'activated' => 1
            ]);
        } catch (Exception $e) {
            Log::info($e);
        }

        try {
            User::create([
                'name' => 'Скидоботик',
                'email' => "skidobot@gmail.com",
                'password' => bcrypt('skidobotsecret'),
                'fio_from_telegram' => "Скидоботик",
                'source' => '000',
                'telegram_chat_id' => "1234567890",
                'referrals_count' => 0,
                'referral_bonus_count' => 0,
                'cashback_bonus_count' => 0,
                'is_admin' => true,
                'activated' => 1
            ]);
        } catch (Exception $e) {
            Log::info($e);
        }

        $users = User::where("parent_id",null)->get();

        $skidobot = User::where("email", "skidobot@gmail.com")->first();

        foreach ($users as $user){
            $user->parent_id = $skidobot->id;
        }

    }
}
