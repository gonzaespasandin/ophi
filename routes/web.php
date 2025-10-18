<?php

use Illuminate\Support\Facades\Route;


// Home

Route::get('/', [\App\Http\Controllers\HomeController::class, 'home'])
    ->name('home')
    ->middleware('auth');


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
    ->name('auth.register.show')
    ->middleware('guest');

Route::post('/registrarse/usuario', [\App\Http\Controllers\RegisterController::class, 'process'])
    ->name('auth.register.process')
    ->middleware('guest');

/*
    ------------------------------------------------------------
    ------------------------ REGISTER --------------------------
    ------------------------- médico ---------------------------
*/

Route::get('/registrarse/perfil-medico/', [\App\Http\Controllers\MedicalProfileController::class, 'create'])
    ->name('profile.create.show')
    ->middleware('auth');

Route::post('/registrarse/perfil-medico', [\App\Http\Controllers\MedicalProfileController::class, 'store'])
    ->name('profile.create.store')
    ->middleware('auth');


/*
    ------------------------------------------------------------
    ------------------------ LOGIN -----------------------------
    ------------------------------------------------------------
*/

Route::get('/iniciar-sesion', [\App\Http\Controllers\AuthController::class, 'login'])
    ->name('auth.login.show')
    ->middleware('guest');

Route::post('/iniciar-sesion', [\App\Http\Controllers\AuthController::class, 'process'])
    ->name('auth.login.process')
    ->middleware('guest');




Route::post('/cerrar-sesion', [\App\Http\Controllers\AuthController::class, 'logoutProcess'])
     ->name('auth.logout.process')
     ->middleware('auth');


/*
    ------------------------------------------------------------
    -------------------- USER PROFILE --------------------------
    ------------------------------------------------------------
*/


Route::get('/perfil', [\App\Http\Controllers\UserProfileController::class, 'user'])
    ->name('profile.userProfile')
    ->middleware('auth');

Route::post('/perfil', [\App\Http\Controllers\UserProfileController::class, 'getUserOwnMedicalProfile'])
    ->name('profile.userProfile.process')
    ->middleware('auth');