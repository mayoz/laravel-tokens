<?php

namespace Mayoz\Token;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class TokenServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/laravel-tokens.php' => config_path('laravel-tokens.php'),
            ], 'laravel-tokens-config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'laravel-tokens-migrations');
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravel-tokens.php',
            'laravel-tokens'
        );

        $this->registerGuard();
    }

    /**
     * Register the token guard.
     *
     * @return void
     */
    protected function registerGuard()
    {
        Auth::extend('multi-token', function ($app, $name, array $config) {
            return tap($this->makeGuard($config), function ($guard) use ($app) {
                $this->app->refresh('request', $guard, 'setRequest');
            });
        });
    }

    /**
     * Make an instance of the token guard.
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Auth\Guard
     */
    protected function makeGuard(array $config)
    {
        return new TokenGuard(
            Auth::createUserProvider($config['provider']),
            $this->app['request']
        );
    }
}
