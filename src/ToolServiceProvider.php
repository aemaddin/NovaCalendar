<?php

namespace Asciisd\NovaCalendar;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\Facades\Route;
use Asciisd\NovaCalendar\Models\Event;
use Illuminate\Support\ServiceProvider;
use Asciisd\NovaCalendar\Observers\EventObserver;
use Asciisd\NovaCalendar\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'nova-calendar');

        $this->publishes([
            __DIR__
            . '/../database/migrations/create_events_table.php.stub' => database_path('migrations/'
                                                                                      . date('Y_m_d_His',
                    time()) . '_create_events_table.php'),
        ], 'migrations');

        $this->publishes([
            __DIR__
            . '/config/nova-calendar.php' => config_path('nova-calendar.php'),
        ], 'config');

        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            if( ! is_null(config('google-calendar.calendar_id'))) {
                Event::observe(EventObserver::class);
            }

            Nova::provideToScript([
                'fullcalendar_locale' => config('nova-calendar.fullcalendar_locale'),
            ]);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes() {
        if($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova', Authorize::class])
             ->prefix('nova-vendor/nova-calendar')
             ->namespace('Asciisd\NovaCalendar\Http\Controllers')
             ->group(__DIR__ . '/../routes/api.php');

        $this->commands([
            \Asciisd\NovaCalendar\Console\Commands\ImportEvents::class,
            \Asciisd\NovaCalendar\Console\Commands\ExportEvents::class
        ]);
    }
}
