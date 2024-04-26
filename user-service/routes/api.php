<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::prefix('auth')->group(function () {
    Route::post('sign-in', [AuthController::class, 'signInWithEmailPassword']);
    Route::post('sign-in-with-firebase', [AuthController::class, 'signInWithFirebase']);
    Route::post('sign-in-with-oauth', [AuthController::class, 'signInWithOAuth']);
});

Route::middleware('authen')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('sign-out', [AuthController::class, 'signOut']);
        Route::get('profile', [AuthController::class, 'profile']);
    });
});
