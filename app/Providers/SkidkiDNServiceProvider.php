<?php

namespace App\Providers;

use App\Classes\iSkidkiDNBot;
use App\Classes\SkidkiDNBot;
use BotMan\BotMan\BotMan;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class SkidkiDNServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        App::singleton(SkidkiDNBot::class, function() {
            return new SkidkiDNBot();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
