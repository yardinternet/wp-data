<?php

declare(strict_types=1);

namespace Yard\Models\Providers;

use Illuminate\Support\ServiceProvider;
use Yard\Models\Console\ModelCommand;
use Yard\Models\Model;

class ModelServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Model', function () {
            return new Model($this->app);
        });

        $this->mergeConfigFrom(
            __DIR__.'/../../config/model.php',
            'model'
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
            __DIR__.'/../../config/model.php' => $this->app->configPath('model.php'),
        ], 'config');

        $this->loadViewsFrom(
            __DIR__.'/../../resources/views',
            'Model',
        );

        $this->commands([
            ModelCommand::class,
        ]);

        $this->app->make('Model');
    }
}
