<?php

namespace AppccDigital\LaravelAppcc;

use AppccDigital\LaravelAppcc\Commands\TestConnectionCommand;
use AppccDigital\LaravelAppcc\Http\AppccClient;
use Illuminate\Support\ServiceProvider;

class AppccServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/appcc.php', 'appcc');

        $this->app->singleton(AppccManager::class, function ($app) {
            return new AppccManager(
                new AppccClient(
                    url: config('appcc.url'),
                    token: config('appcc.token'),
                    timeout: config('appcc.timeout'),
                ),
            );
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/appcc.php' => config_path('appcc.php'),
        ], 'appcc-config');

        if ($this->app->runningInConsole()) {
            $this->commands([TestConnectionCommand::class]);
        }
    }
}
