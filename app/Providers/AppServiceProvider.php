<?php

namespace App\Providers;

use App\Listeners\RecordDemoChange;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // The schedule mutex must survive demo:reset wiping the database
        // cache store mid-run, so keep it in the file store
        $this->app->resolving(Schedule::class, fn (Schedule $schedule) => $schedule->useCache('file'));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(['eloquent.saved: *', 'eloquent.deleted: *'], RecordDemoChange::class);
    }
}
