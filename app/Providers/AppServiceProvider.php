<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (config('app.env') != 'local') {
            $this->app['request']->server->set('HTTPS', true);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(UrlGenerator $url)
    {
        // if (config('app.env') != 'local') {
        //     URL::forceScheme('https');
        // }
        if(env('REDIRECT_HTTPS')) {
            $url->forceScheme('https');
        }
    }
}
