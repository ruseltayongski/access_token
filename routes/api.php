<?php

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

Route::get('/invalid/token', [App\Http\Controllers\LoginController::class, 'invalidAccessToken'])->name('invalid/token');
Route::prefix('/user')->group(function(){
    Route::post('/login', [App\Http\Controllers\LoginController::class, 'login']);
    Route::middleware('auth:api')->get('/profile', [App\Http\Controllers\LoginController::class, 'getUserProfile']); //protected API
});