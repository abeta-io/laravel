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
    public function boot()
    {
        $this->app->singleton('abeta', function ($app) {
            return new AbetaPunchOut;
        });
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
    