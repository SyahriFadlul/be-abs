<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // if ($this->app->isLocal()) {
        //     //if local register your services you require for development
        //         $this->app->register('Barryvdh\Debugbar\ServiceProvider');
        //     } else {
        //     //else register your services you require for production
        //         $this->app['request']->server->set('HTTPS', true);
        //     }
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // URL::forceScheme('https');
         Schema::defaultStringLength(191);
    }
}
