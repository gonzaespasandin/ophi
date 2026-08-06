<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController as ApiBrandController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\CategoryController as ApiCategoryController;
use App\Http\Controllers\Api\HistoryController;
use App\Http\Controllers\Api\IngredientController;
use App\Http\Controllers\Api\NewsletterSubscriberController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ScannerController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\Admin\AdminCatalogController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Middleware\EnsureIsAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

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



/** ACCOUNT */
Route::middleware(['auth:sanctum'])
    ->prefix('account')
    ->group(function () {
        Route::get('/', [AccountController::class, 'show']);
        Route::put('/email', [AccountController::class, 'updateEmail'])->middleware('throttle:6,1');
        Route::put('/newsletter', [AccountController::class, 'updateNewsletter']);
    });

Route::post('/account/email/confirm', [AccountController::class, 'confirmEmail'])
    ->middleware('throttle:10,1');



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

        Route::get('/products/search', [ProductController::class, 'search']);
        Route::get('/products/nameandbrand/{name}/{brand}', [ProductController::class, 'find_by_name_and_brand']);
        Route::get('/products/matchedname/{name}', [ProductController::class, 'find_match_by_name']);
        Route::post('/products/recomended', [ProductController::class, 'getRecomendedProducts']);
        Route::get('/products/safe', [ProductController::class, 'getSafeProducts']);
    });



/** ESCANNER */
Route::middleware(['auth:sanctum'])
    ->group(function () {
        Route::post('/scanner/process', [ScannerController::class, 'process']);
        Route::get('/scanner/can-suggest', [ScannerController::class, 'canSuggest']);
        Route::post('/scanner/suggest', [ScannerController::class, 'suggest']);
        Route::get('/scanner/pending-barcode', [ScannerController::class, 'getPendingBarcode']);
        Route::delete('/scanner/clear-pending-barcode', [ScannerController::class, 'clearPendingBarcode']);
    });

/** HISTORY */
Route::middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/history', [HistoryController::class, 'index']);
        Route::post('/history', [HistoryController::class, 'store']);
        Route::get('/history/latest', [HistoryController::class, 'getLatestScans']);
        Route::get('/history/count', [HistoryController::class, 'countScans']);
        Route::get('/history/name/{name}', [HistoryController::class, 'searchByName']);
    });


/** SUBSCRIPTION */
Route::middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/subscription', [SubscriptionController::class, 'getSubscription']);
        Route::get('/givePremium', [SubscriptionController::class, 'givePremium']);
        Route::get('/giveFree', [SubscriptionController::class, 'giveFree']);

    });


/** FILTERS */
Route::middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/brands', [ApiBrandController::class, 'getBrands']);
        Route::get('/brands/{name}', [ApiBrandController::class, 'getBrandsByName']);
        Route::get('/origins', [ProductController::class, 'getOrigins']);
        Route::get('/categories', [ApiCategoryController::class, 'getCategories']);
    });

/** SUBSCRIPTION (LANDING PAGE) */
Route::post('/subscribe-email', [NewsletterSubscriberController::class, 'subscribe']);



/** PRODUCTOS OPHI POR EAN/BARCODE */
Route::middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/catalog/{ean}', [CatalogController::class, 'findByEan'])
            ->where('ean', '[0-9]+');
    });



/** ADMIN - OCR de ingredientes sobre productos Ophi */
Route::middleware(['auth:sanctum', EnsureIsAdmin::class])
    ->prefix('admin')
    ->group(function () {
        Route::get('/catalog/{ean}', [AdminCatalogController::class, 'lookup'])
            ->where('ean', '[0-9]+');
        Route::post('/catalog/{ean}', [AdminCatalogController::class, 'create'])
            ->where('ean', '[0-9]+');
        Route::get('/catalog/{ean}/similar', [AdminCatalogController::class, 'similar'])
            ->where('ean', '[0-9]+');
        Route::post('/catalog/{ean}/extract-ingredients', [AdminCatalogController::class, 'extractIngredients'])
            ->where('ean', '[0-9]+');
        Route::put('/catalog/{ean}/ingredients', [AdminCatalogController::class, 'saveIngredients'])
            ->where('ean', '[0-9]+');
    });
