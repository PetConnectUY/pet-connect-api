<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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
    'middleware' => 'jwt.auth',
], function(){
    Route::controller(UserController::class)->group(function(){
        Route::post('', 'store');
        Route::post('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
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