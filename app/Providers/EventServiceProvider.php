<?php

namespace App\Providers;

use App\Events\AchievementEvent;
use App\Events\ActivateUserEvent;
use App\Events\NetworkCashBackEvent;
use App\Events\NetworkLevelRecounterEvent;
use App\Listeners\AchievementProcessor;
use App\Listeners\NetworkCashBackProcessor;
use App\Listeners\NetworkLevelRecounterProcessor;
use App\Listeners\UserStatusHandler;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        AchievementEvent::class => [
            AchievementProcessor::class
        ],

        NetworkCashBackEvent::class => [
            NetworkCashBackProcessor::class
        ],

        NetworkLevelRecounterEvent::class => [
            NetworkLevelRecounterProcessor::class
        ],
        ActivateUserEvent::class => [
            UserStatusHandler::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }

    public function shouldDiscoverEvents()
    {
        return true;
    }

    protected function discoverEventsWithin()
    {
        return [
            $this->app->path('Listeners'),
        ];
    }
}
