<?php

use App\Http\Controllers\StateController;
use App\Http\Controllers\AdController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;



Route::get('/ping', function(){
    return ['pong' => true];
});

Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');

Route::get('/states', [StateController::class, 'getStates']);
Route::get('/categories', [AdController::class, 'getCategories']);

Route::get('/ads', [AdController::class, 'getAds']);

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout']);
Route::post('/auth/refresh', [AuthController::class, 'refreshToken']);

Route::middleware('auth:api')->group(function(){

    Route::get('/user', [UserController::class, 'getLoggedUserInfo']);
    Route::put('/user', [UserController::class, 'editLoggedUserInfo']);
    
    
    Route::post('/ad/edit/{id}', [AdController::class, 'editAd']);
    Route::post('/ad', [AdController::class, 'newAd']);
    Route::get('/ad/{id}', [AdController::class, 'getItem']);
    Route::post('/ad/delete/{id}', [AdController::class, 'deleteAd']);
});
