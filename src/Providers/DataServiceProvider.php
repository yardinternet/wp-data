<?php

declare(strict_types=1);

namespace Yard\Data\Providers;

use Illuminate\Support\ServiceProvider;

class DataServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/yard-data.php',
            'yard-data'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/yard-data.php' => $this->app->configPath('yard-data.php'),
        ], 'config');
    }
}
