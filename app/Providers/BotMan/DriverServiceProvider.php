<?php

namespace App\Providers\BotMan;

use App\Drivers\CustomTelegramDriver;
use App\Drivers\TelegramInlineQueryDriver;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Telegram\TelegramDriver;
use BotMan\Studio\Providers\DriverServiceProvider as ServiceProvider;

class DriverServiceProvider extends ServiceProvider
{
    /**
     * The drivers that should be loaded to
     * use with BotMan
     *
     * @var array
     */
    protected $drivers = [
        CustomTelegramDriver::class,
       // TelegramInlineQueryDriver::class
    ];

    /**
     * @return void
     */
    public function boot()
    {
       // parent::boot();

        foreach ($this->drivers as $driver) {
            DriverManager::loadDriver($driver);
        }
    }
}
