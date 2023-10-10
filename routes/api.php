<?php

use App\Classes\UserRole;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MercadoPagoController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\PetImageController;
use App\Http\Controllers\PetProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QrCodeActivationController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\PetController as DashboardPetController;
use App\Http\Controllers\Dashboard\UserController as DashboardUserController;
use App\Http\Controllers\Dashboard\QrCodeController as DashboardQrCodesController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Auth routes
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('jwt.refresh')->group(function () {
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
    
    Route::middleware('jwt.auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

// Users routes
Route::prefix('users')->group(function () {
    Route::post('', [UserController::class, 'store']);
    Route::get('/check-username-exists/{username}', [UserController::class, 'checkUsernameAvailability']);
    Route::get('/check-email-exists/{email}', [UserController::class, 'checkEmailAvailability']);
    
    Route::middleware('jwt.auth')->group(function () {
        Route::post('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::get('/statistics', [StatisticController::class, 'userStatistic']);
    });
});

// Pets routes
Route::prefix('pets')->middleware(['jwt.auth', 'role.checker:' . implode(',', [UserRole::USER_ROLE, UserRole::PREMIUM_ROLE, UserRole::PREMIUM_PLUS, UserRole::AFFILIATE, UserRole::ADMIN_ROLE])])->group(function () {
    Route::controller(PetController::class)->group(function () {
        Route::get('', 'index');
        Route::get('/{id}', 'view');
        Route::post('', 'store');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });
});

// Pet Images routes
Route::prefix('pets-images')->middleware(['jwt.auth', 'role.checker:' . implode(',', [UserRole::USER_ROLE, UserRole::PREMIUM_ROLE, UserRole::PREMIUM_PLUS, UserRole::AFFILIATE, UserRole::ADMIN_ROLE])])->group(function () {
    Route::controller(PetImageController::class)->group(function () {
        Route::post('', 'store');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
        Route::get('/{id}', 'getImage');
    });
});

// Roles routes
Route::prefix('roles')->middleware(['jwt.auth', 'role.checker:' . implode(',', [UserRole::ADMIN_ROLE])])->group(function () {
    Route::post('', [RoleController::class, 'store']);
    Route::delete('/{id}', [RoleController::class, 'destroy']);
});

//Products routes
Route::prefix('products')->middleware(['jwt.auth'])->group(function() {
    Route::get('', [ProductController::class, 'index']);
    Route::middleware('role.checker:' . implode(',', [UserRole::ADMIN_ROLE]))->group(function () {
        Route::post('', [ProductController::class, 'store']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
    });
});

// Mercado Pago routes
Route::prefix('store')->group(function() {
    Route::post('/webhook', [MercadoPagoController::class, 'handleWebhook'])->name('store.webhook');
    Route::middleware('jwt.auth')->group(function () {
        Route::post('/{productId}', [MercadoPagoController::class, 'createOrder']);
    });
});

Route::prefix('qr-codes')->group(function() {
    Route::middleware('role.checker:' . implode(',', [UserRole::ADMIN_ROLE]))
        ->group(function() {
            Route::post('', [QrCodeController::class, 'generate']);
            Route::post('generate-image', [QrCodeController::class, 'generateQrImage']);
        });
    Route::middleware('jwt.auth')
        ->group(function() {
            Route::post('activate/{activationToken}', [QrCodeActivationController::class, 'activate']);
            Route::post('activate/set-user/{activationToken}', [QrCodeActivationController::class, 'activateQrWithUser']);
        });
    Route::get('verify-activation/{activationToken}', [QrCodeActivationController::class, 'verifyQrActivation']);
});

Route::prefix('pet-profiles')->group(function() {
    Route::get('/{token}', [PetProfileController::class, 'view']);
    Route::post('/{token}/pet-found', [PetProfileController::class, 'petFound'])->middleware('throttle:1,3');
});


Route::prefix('dashboard')->middleware((['jwt.auth']))->group(function() {
    Route::get('/my-pets', [DashboardPetController::class, 'getPets']);
    Route::get('/my-codes', [DashboardQrCodesController::class, 'getQrCodes']);
    Route::post('/change-settings', [DashboardUserController::class, 'changeSettings']);
    Route::get('/get-settings', [DashboardUserController::class, 'getSettings']);
});