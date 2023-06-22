<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\GenresController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UpdateProfileController;
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

Route::middleware('auth:sanctum')->group(function () {
    Route::group(['controller' => MovieController::class], function () {
        Route::post('/movie/create', 'store')->name('create.movie');
        Route::get('/movies', 'fetch')->name('fetch.movies');
        Route::get('/movie', 'show')->name('fetch.movie');
        Route::delete('/movie/delete/{movie}', 'destroy')->name('destroy.movie');
        Route::post('/movie/update/{movieId}', 'update')->name('update.movie');
        Route::get('/movie-for-quotes', 'getMoviesForQuote')->name('get.movies.for.quote');
    });

    Route::group(['controller' => QuoteController::class], function () {
        Route::get('/movie/quotes', 'getQuotesForMovie')->name('get.quote.for.movie');
        Route::post('/quote/create', 'store')->name('create.quote');
        Route::get('/movie/quote', 'getQuote')->name('get.quote');
        Route::delete('/quote/delete/{quote}', 'destroy')->name('destroy.quote');
        Route::post('/quote/update/{quote}', 'update')->name('update.quote');
        Route::get('/quotes/all', 'all')->name('get.all.quotes');
    });

    Route::group(['controller' => CommentController::class, 'prefix' => '/comment'], function () {
        Route::post('/create', 'store')->name('comment.create');
    });

    Route::group(['controller' => LikeController::class, 'prefix' => '/like'], function () {
        Route::post('/create', 'store')->name('like.create');
    });

    Route::get('/genres', [GenresController::class, 'getGenres'])->name('get.genres');

    Route::group(['controller' => AuthController::class], function () {
        Route::get('/user', 'user')->name('auth.data');
        Route::get('/logout', 'logout')->name('auth.logout');
    });

    Route::group(['controller' => NotificationController::class], function () {
        Route::get('/notifications', 'all')->name('get.all.notifications');
        Route::get('/notifications/count', 'getNotSeen')->name('get.not.seen.notifications.count');
        Route::post('/notifications/read-all', 'readAll')->name('read.all');
        Route::patch('/read/notification/{notifyId}', 'read')->name('post.read.notification');
    });

    Route::group(['controller' => UpdateProfileController::class], function () {
        Route::post('/profile/update', 'update')->name('profile.update');
        Route::post('/email', 'UpdateEmail')->name('email');
    });
});

Route::group(['prefix' => '/auth/google', 'controller' => GoogleAuthController::class], function () {
    Route::get('/redirect', 'redirect');
    Route::get('/callback', 'callback');
});
