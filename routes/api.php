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

Route::group(['prefix' => 'auth'], function(){
    Route::post("login", [AuthController::class, 'login']);
    Route::group(['middleware' => ['jwt.auth']], function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });
    Route::group(['middleware' => 'jwt.refresh'], function () {
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});

Route::group([
    'prefix' => 'users',
], function(){
    Route::post('', [UserController::class, 'store']);
    Route::group(['middleware' => ['jwt.auth']], function() {
        Route::post('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });
});

Route::group([
    'prefix' => 'pets',
    'middleware' => 'jwt.auth',
], function(){
    Route::controller(PetController::class)->group(function(){
        Route::post('', 'store');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });
});

Route::group([
    'prefix' => 'pets-images',
    'middleware' => 'jwt.auth',
], function(){
    Route::controller(PetImageController::class)->group(function(){
        Route::post('', 'store');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });
});

Route::group([
    'prefix' => 'roles',
    'middleware' => [
        'jwt.auth',
        'role.checker:'.implode(',', [UserRole::ADMIN_ROLE])
    ],
], function(){
    Route::post('', [RoleController::class, 'store']);
    Route::delete('/{id}', [RoleController::class, 'destroy']);
});

Route::group([
    'prefix' => 'users-pets-tokens',
    'middleware' => [
        'jwt.auth',
        'role.limits'
    ]
], function(){
    Route::post('', [UserPetTokenController::class, 'generateQRCode']);
});
