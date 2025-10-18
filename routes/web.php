<?php

use Illuminate\Support\Facades\Route;


// Home

Route::get('/', [\App\Http\Controllers\HomeController::class, 'home'])
    ->name('home');


/*
    ------------------------------------------------------------
    ------------------------ REGISTER --------------------------
    ------------------------------------------------------------
*/


/*
    ------------------------------------------------------------
    ------------------------ REGISTER --------------------------
    -------------------------- user ----------------------------
*/

Route::get('/registrarse/usuario', [\App\Http\Controllers\RegisterController::class, 'register'])
    ->name('auth.register.show');

Route::post('/registrarse/usuario', [\App\Http\Controllers\RegisterController::class, 'process'])
     ->name('auth.register.process');

/*
    ------------------------------------------------------------
    ------------------------ REGISTER --------------------------
    ------------------------- médico ---------------------------
*/

Route::get('/registrarse/perfil-medico/', [\App\Http\Controllers\MedicalProfileController::class, 'create'])
    ->name('profile.create.show');

Route::post('/registrarse/perfil-medico', [\App\Http\Controllers\MedicalProfileController::class, 'store'])
    ->name('profile.create.store');


/*
    ------------------------------------------------------------
    ------------------------ LOGIN -----------------------------
    ------------------------------------------------------------
*/

Route::get('/iniciar-sesion', [\App\Http\Controllers\AuthController::class, 'login'])
    ->name('auth.login.show');

Route::post('/iniciar-sesion', [\App\Http\Controllers\AuthController::class, 'process'])
     ->name('auth.login.process');




Route::post('/cerrar-sesion', [\App\Http\Controllers\AuthController::class, 'logoutProcess'])
     ->name('auth.logout.process');

/*
    ------------------------------------------------------------
    ------------------------ SCANNER ---------------------------
    ------------------------------------------------------------
*/

Route::get('/escaner', [\App\Http\Controllers\ScannerController::class, 'index'])
    ->name('scanner.index');

Route::post('/escaner/procesar', [\App\Http\Controllers\ScannerController::class, 'process'])
    ->name('scanner.process');
