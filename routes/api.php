<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HistoryController;
use App\Http\Controllers\Api\IngredientController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ScannerController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

/** AUTHENTICATION */
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/forgot-password', [AuthController::class, 'forgot_password']);
Route::post('/reset-password', [AuthController::class, 'reset_password']);



/** PROFILES */
Route::get('/user-profiles', [ProfileController::class, 'get_auth_user_profiles'])->middleware('auth:sanctum');
Route::post('/profiles', [ProfileController::class, 'store'])->middleware('auth:sanctum');
Route::put('/profiles/{id}', [ProfileController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/profiles/{id}', [ProfileController::class, 'destroy'])->middleware('auth:sanctum');



/** INGREDIENTS */
Route::get('/ingredients', [IngredientController::class, 'all']);
Route::get('/intolerances', [IngredientController::class, 'intolerances']);
Route::get('/allergies', [IngredientController::class, 'allergies']);
Route::get('/special-diets', [IngredientController::class, 'special_diets']);



/** PRODUCTS */
Route::middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/products', [ProductController::class, 'all']);
        Route::get('/products/{id}/', [ProductController::class, 'find'])->whereNumber('id');
        Route::get('/products/barcode/{barcode}', [ProductController::class, 'find_by_barcode']);

        Route::get('/products/name/{name}', [ProductController::class, 'find_by_name']);
        Route::get('/products/nameandbrand/{name}/{brand}', [ProductController::class, 'find_by_name_and_brand']);
        Route::get('/products/matchedname/{name}', [ProductController::class, 'find_match_by_name']);
    });
Route::post('/products/recomended', [ProductController::class, 'getRecomendedProducts']);


/** ESCANNER */
Route::post('/scanner/process', [ScannerController::class, 'process'])->middleware('auth:sanctum');

/** HISTORY */
Route::middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/history', [HistoryController::class, 'index']);
        Route::post('/history', [HistoryController::class, 'store']);
    });
