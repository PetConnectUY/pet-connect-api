<?php

use App\Classes\UserRole;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\PetImageController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPetTokenController;
use Illuminate\Support\Facades\Route;

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
    
    Route::middleware('jwt.auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});

// Users routes
Route::prefix('users')->group(function () {
    Route::post('', [UserController::class, 'store']);
    
    Route::middleware('jwt.auth')->group(function () {
        Route::post('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });
});

// Pets routes
Route::prefix('pets')->middleware(['jwt.auth', 'role.checker:' . implode(',', [UserRole::USER_ROLE, UserRole::PREMIUM_ROLE, UserRole::ADMIN_ROLE])])->group(function () {
    Route::controller(PetController::class)->group(function () {
        Route::get('', 'index');
        Route::get('/{id}', 'view');
        Route::post('', 'store');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });
});

// Pet Images routes
Route::prefix('pets-images')->middleware(['jwt.auth', 'role.checker:' . implode(',', [UserRole::USER_ROLE, UserRole::PREMIUM_ROLE, UserRole::ADMIN_ROLE])])->group(function () {
    Route::controller(PetImageController::class)->group(function () {
        Route::post('', 'store');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });
});

// Roles routes
Route::prefix('roles')->middleware(['jwt.auth', 'role.checker:' . implode(',', [UserRole::ADMIN_ROLE])])->group(function () {
    Route::post('', [RoleController::class, 'store']);
    Route::delete('/{id}', [RoleController::class, 'destroy']);
});

// Users Pets Tokens routes
Route::prefix('users-pets-tokens')->middleware(['jwt.auth', 'role.checker:' . implode(',', [UserRole::USER_ROLE, UserRole::PREMIUM_ROLE, UserRole::ADMIN_ROLE])])->group(function () {
    Route::delete('/{id}', [UserPetTokenController::class, 'destroy']);
    Route::get('/', [UserPetTokenController::class, 'trashed']);
    
    Route::middleware('role.limits')->group(function () {
        Route::post('', [UserPetTokenController::class, 'generateToken']);
        Route::post('/{id}/restore', [UserPetTokenController::class, 'restoreTrashed']);
    });
});