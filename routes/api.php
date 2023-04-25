<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('auth/login', [\App\Http\Controllers\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (){
    Route::group(['prefix' => 'user/'],function (){
        Route::put('{user}', [\App\Http\Controllers\RequestController::class, 'updateUser']);
        Route::delete('{user}', [\App\Http\Controllers\RequestController::class, 'deleteUser']);
        Route::post('register', [\App\Http\Controllers\RequestController::class, 'createUser']);
    });
    Route::group(['prefix' => 'user/request/'],function (){
        Route::patch('approve/{request}', [\App\Http\Controllers\RequestController::class, 'approve']);
        Route::patch('decline/{request}', [\App\Http\Controllers\RequestController::class, 'decline']);
    });
});
