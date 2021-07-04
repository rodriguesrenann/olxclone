<?php

use App\Http\Controllers\StateController;
use App\Http\Controllers\AdController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/ping', function(){
    return ['pong' => true];
});

Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');

Route::get('/states', [StateController::class, 'getStates']);
Route::get('/categories', [AdController::class, 'getCategories']);

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout']);
Route::post('/auth/refresh', [AuthController::class, 'refreshToken']);

Route::middleware('auth:api')->group(function(){

    Route::get('/user', [UserController::class, 'getLoggedUserInfo']);
    Route::put('/user', [UserController::class, 'editLoggedUserInfo']);
 
    Route::post('/ad', [AdController::class, 'newAd']);
    Route::get('/ad', [AdController::class, 'getAds']);
    Route::get('/ad/{id}', [AdController::class, 'getItem']);
    Route::post('/ad/{id}', [AdController::class, 'editAd']);
});
