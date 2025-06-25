<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //

        Gate::define('viewLogViewer', function($user) {
            return $user->role == 'administrator';
        });

        if(env('APP_ENV', 'production') == 'production') { // use https only if env is production
            URL::forceScheme('https');
        }
    }
}
