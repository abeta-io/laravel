<?php

namespace AbetaIO\Laravel;

use Illuminate\Support\ServiceProvider;


class AbetaServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('abeta', function ($app) {
            return new AbetaPunchOut();
        });

        // Load config
        $this->mergeConfigFrom(__DIR__ . '/../config/abeta.php', 'abeta');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePublishing();

        if (config('abeta.routes.load')) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/abeta.php');
        }
    }

    protected function configurePublishing()
    {
        // Publish config file
        $this->publishes(
            [__DIR__ . '/../config' => $this->app->basePath('config')],
            ['abeta', 'abeta-config']
        );

        // Publish routes file
        $this->publishes(
            [__DIR__ . '/../routes' => $this->app->basePath('routes')],
            ['abeta', 'abeta-routes']
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['abeta'];
    }
}
    