<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UpdateProfileController;
use Illuminate\Support\Facades\Auth;
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

Route::group(['controller' => RegisterController::class], function () {
    Route::post('/register', 'store')->name('register.store');
    Route::post('/verify-email', 'verify')->name('register.verify');
});

Route::group(['controller' => ForgotPasswordController::class, 'prefix' => '/recover'], function () {
    Route::post('/email', 'sendEmail')->name('recover.send.email');
    Route::post('/password', 'reset')->name('recover.reset.password');
});

Route::group(['controller' => AuthController::class], function () {
    Route::post('/login', 'login')->name('auth.login');
});

Route::group(['middleware' => ['auth:sanctum'], 'controller' => AuthController::class], function () {
    Route::get('/user', 'user')->name('auth.data');
    Route::get('/logout', 'logout')->name('auth.logout');
});

Route::group(['middleware' => ['auth:sanctum'], 'controller' => UpdateProfileController::class], function () {
    Route::post('/profile/update', 'update')->name('profile.update');
});

Route::group(['prefix' => '/auth/google', 'controller' => GoogleAuthController::class], function () {
    Route::get('/redirect', 'redirect');
    Route::get('/callback', 'callback');
});
