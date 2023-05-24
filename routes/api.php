<?php

use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\RegisterController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['controller' => RegisterController::class], function () {
    Route::post('/register', 'store')->name('register.store');
    Route::post('/verify-email', 'verify')->name('register.verify');
});

Route::group(['controller' => ForgotPasswordController::class, 'prefix' => '/recover'], function () {
    Route::post('/email', 'sendEmail')->name('recover.send.email');
    Route::post('/password', 'reset')->name('recover.reset.password');
});
