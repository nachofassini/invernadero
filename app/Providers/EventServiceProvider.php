<?php

namespace App\Providers;

use App\Models\Activation;
use App\Models\Crop;
use App\Models\Deviation;
use App\Models\Measure;
use App\Observers\ActivationObserver;
use App\Observers\CropObserver;
use App\Observers\DeviationObserver;
use App\Observers\MeasureObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Activation::observe(ActivationObserver::class);
        Crop::observe(CropObserver::class);
        Deviation::observe(DeviationObserver::class);
        Measure::observe(MeasureObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
