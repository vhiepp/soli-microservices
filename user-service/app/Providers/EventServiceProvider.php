<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Jobs\TestJob;
use App\Jobs\UserCreatedJob;

class EventServiceProvider extends ServiceProvider
{
    // /**
    //  * The event to listener mappings for the application.
    //  *
    //  * @var array<class-string, array<int, class-string>>
    //  */
    // protected $listen = [
    //     Registered::class => [
    //         SendEmailVerificationNotification::class,
    //     ],
    // ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        \App::bindMethod(TestJob::class . "@handle", fn($job) => $job->handle());
        \App::bindMethod(UserCreatedJob::class . "@handle", fn($job) => $job->handle());
    }

    // /**
    //  * Determine if events and listeners should be automatically discovered.
    //  */
    // public function shouldDiscoverEvents(): bool
    // {
    //     return false;
    // }
}
