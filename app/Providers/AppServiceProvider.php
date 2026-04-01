<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
    public function boot()
    {
        Paginator::useBootstrapFive();

        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return config('app.spa_url') . '/reset-password/' . $token . '/' . $user->email;
        });

        /*Forzar HTTPS en producción
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        */
    }
}