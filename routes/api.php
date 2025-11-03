<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\IngredientController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

/** AUTHENTICATION */
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);



/** PROFILES */
Route::get('/user-profiles', [ProfileController::class, 'get_auth_user_profiles'])->middleware('auth:sanctum');
Route::post('/profiles', [ProfileController::class, 'store'])->middleware('auth:sanctum');



/** INGREDIENTS */
Route::get('/ingredients', [IngredientController::class, 'all']);



/** PRODUCTS */
Route::middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/products', [ProductController::class, 'all']);
        Route::get('/products/{id}/', [ProductController::class, 'find'])->whereNumber('id');
        Route::get('/products/barcode/{barcode}', [ProductController::class, 'find_by_barcode']);

        Route::get('/products/name/{name}', [ProductController::class, 'find_by_name']);
    });
