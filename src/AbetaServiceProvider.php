<?php
declare(strict_types=1);

namespace AbetaIO\Laravel;

use AbetaIO\Laravel\Services\Cart\CartBuilder;
use AbetaIO\Laravel\Services\Cart\ReturnCart;
use Illuminate\Support\ServiceProvider;


class AbetaServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() : void
    {
        $this->app->singleton('abeta', function ($app) {
            return new AbetaPunchOut();
        });

        $this->app->singleton('return-cart', function ($app) {
            return new ReturnCart(new CartBuilder());
        });

        // Load config
        $this->mergeConfigFrom(__DIR__ . '/../config/abeta.php', 'abeta');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() : void
    {
        $this->configurePublishing();

        $this->app['router']->middlewareGroup('abeta_session', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Session\Middleware\StartSession::class
        ]);

        if (config('abeta.routes.load')) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/abeta.php');
        }
    }

    protected function configurePublishing() : void
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
    public function provides() : array
    {
        return ['abeta', 'return-cart'];
    }
}
    