<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Session;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Redirección para web (admin)
        $middleware->redirectGuestsTo(function() {
            Session::flash('feedback.message', 'Tenés que iniciar sesión para proceder');
            Session::flash('feedback.type', 'warning');

            return route('login.show');
        });

        // 👉 Esto es CLAVE para Sanctum + SPA
        $middleware->appendToGroup('api', EnsureFrontendRequestsAreStateful::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();