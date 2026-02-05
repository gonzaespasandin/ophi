<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\BrandController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\IngredientController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\UserController;
use App\Http\Middleware\EnsureIsAdmin;
use Illuminate\Support\Facades\Route;


/** AUTHENTICATION */
Route::view('/login/', 'login')
    ->name('login.show');
Route::post('/login/', [AuthController::class, 'login'])
    ->name('login.post');
Route::post('/logout/', [AuthController::class, 'logout'])
    ->name('logout.post');


/** DASHBOARD */
Route::prefix('/')
    ->middleware(['auth', EnsureIsAdmin::class])
    ->group(function() {
        Route::view('/', 'admin')
            ->name('admin.index');


        /** PRODUCTS */
        Route::get('/products', [ProductController::class, 'index'])
            ->name('admin.products');
        Route::get('/products/create', [ProductController::class, 'create'])
            ->name('admin.products.create');
        Route::post('/products/create', [ProductController::class, 'store'])
            ->name('admin.products.store');
        Route::get('/products/{id}/edit', [ProductController::class, 'edit'])
            ->name('admin.products.edit');
        Route::patch('/products/{id}/edit', [ProductController::class, 'update'])
            ->name('admin.products.update');
        Route::delete('/products/', [ProductController::class, 'destroy'])
            ->name('admin.products.destroy');


        /** BRANDS */
        Route::get('/brands', [BrandController::class, 'index'])
            ->name('admin.brands');
        Route::post('/brands', [BrandController::class, 'store'])
            ->name('admin.brands.store');
        Route::put('/brands/', [BrandController::class, 'update'])
            ->name('admin.brands.update');
        Route::delete('/brands/', [BrandController::class, 'destroy'])
            ->name('admin.brands.destroy');


        /** CATEGORIES */
        Route::get('/categories', [CategoryController::class, 'index'])
            ->name('admin.categories');
        Route::post('/categories', [CategoryController::class, 'store'])
            ->name('admin.categories.store');
        Route::put('/categories/', [CategoryController::class, 'update'])
            ->name('admin.categories.update');
        Route::delete('/categories/', [CategoryController::class, 'destroy'])
            ->name('admin.categories.destroy');


        /** INGREDIENTS */
        Route::get('/ingredients', [IngredientController::class, 'index'])
            ->name('admin.ingredients');
        Route::get('/ingredients/create', [IngredientController::class, 'create'])
            ->name('admin.ingredients.create');
        Route::post('/ingredients/create', [IngredientController::class, 'store'])
            ->name('admin.ingredients.store');
        Route::get('/ingredients/{id}/edit', [IngredientController::class, 'edit'])
            ->name('admin.ingredients.edit');
        Route::patch('/ingredients/{id}/edit', [IngredientController::class, 'update'])
            ->name('admin.ingredients.update');
        Route::delete('/ingredients/', [IngredientController::class, 'destroy'])
            ->name('admin.ingredients.destroy');


        /** PREMIUM */
        Route::view('/premium', 'premium')
            ->name('admin.premium');


        /** USERS */
        Route::get('/users', [UserController::class, 'index'])
            ->name('admin.users');
        Route::get('/users/{user}', [UserController::class, 'show'])
            ->name('admin.users.show');
    });
