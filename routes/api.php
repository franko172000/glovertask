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

Route::post('auth/login', [\App\Http\Controllers\AuthController::class, 'login'])->name('login');
Route::post('admin/user', [\App\Http\Controllers\AdminController::class, 'createUser'])->name('create.admin');

Route::middleware('auth:sanctum')->group(function (){
    Route::group(['prefix' => 'user'],function (){
        Route::put('/{user}', [\App\Http\Controllers\RequestController::class, 'updateUser'])->name('user.update.request');
        Route::delete('/{user}', [\App\Http\Controllers\RequestController::class, 'deleteUser'])->name('user.delete.request');
        Route::post('/register', [\App\Http\Controllers\RequestController::class, 'createUser'])->name('user.create.request');
    });
    Route::group(['prefix' => 'user/requests'],function (){
        Route::get('/', [\App\Http\Controllers\RequestController::class, 'getRequests']);
        Route::post('/approve/{request}', [\App\Http\Controllers\RequestController::class, 'approve'])->name('user.approve.request');
        Route::post('/decline/{request}', [\App\Http\Controllers\RequestController::class, 'decline'])->name('user.decline.request');
    });
});
