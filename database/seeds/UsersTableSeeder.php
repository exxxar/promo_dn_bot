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
                'email' => "exxxar@gmail.com",
                'password' => bcrypt('adminsecret'),
                'fio_from_telegram' => "Алексей Гукай",
                'source' => '000',
                'telegram_chat_id' => "484698703",
                'referrals_count' => 0,
                'referral_bonus_count' => 10000,
                'cashback_bonus_count' => 10000,
                'is_admin' => true
            ]);
        } catch (Exception $e) {
            Log::info($e);
        }
    }
}
