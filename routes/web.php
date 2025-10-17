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
