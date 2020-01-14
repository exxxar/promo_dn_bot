<?php

use Illuminate\Database\Seeder;

class PrizesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = \App\Company::all();

        foreach ($companies as $company)
            for ($i = 0; $i < 40; $i++) {
                \App\Prize::create([
                    'title' => "Test prize $i",
                    'description' => "Test description $i",
                    'image_url' => "https://sun9-22.userapi.com/c854220/v854220254/1c23c4/JzYJ7foFtBw.jpg",
                    'company_id' => $company->id,
                    'summary_activation_count' => 10000,
                    'current_activation_count' > 0,

                    'is_active' => true

                ]);
            }
    }
}
