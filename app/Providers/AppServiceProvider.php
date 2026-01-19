<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
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
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            // TODO: Poner la URL de la SPA en el .env (y adaptar los otros lugares en las que la usamos...)
            return 'http://localhost:5173/reset-password/' . $token . '/' . $user->email;
        });

        /*Forzar HTTPS en producción
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        */
    }
}
