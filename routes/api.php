<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Broadcast;
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

// Public routes

Route::get('/email/verify/{id}', [EmailVerificationController::class, 'verify'])->name('verification.verify');

Route::middleware(['guest:sanctum'])->group(function () {
	Route::controller(GoogleAuthController::class)->group(function () {
		Route::get('/auth/redirect', 'redirect');
		Route::get('/auth/callback', 'callback');
	});

	Route::controller(AuthController::class)->group(function () {
		Route::post('/login', 'login')->middleware('guest')->name('auth.login');
		Route::post('/register', 'register')->middleware('guest')->name('auth.register');
	});

	Route::controller(PasswordResetController::class)->group(function () {
		Route::post('/forgot-password', 'check')->middleware('guest')->name('password.email');
		Route::get('/reset-password/{token}', 'redirect')->middleware('guest')->name('password.reset');
		Route::post('/reset-password', 'update')->middleware('guest')->name('password.update');
	});
});

// Protected routes

Route::group(['middleware' => ['auth:sanctum']], function () {
	Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

	Route::controller(UserController::class)->group(function () {
		Route::get('/user', 'index')->name('user.index');
		Route::patch('/user', 'update')->name('user.update');
	});

	Route::prefix('movies')->group(function () {
		Route::controller(MovieController::class)->group(function () {
			Route::get('/', 'index')->name('movies.index');
			Route::get('/{movie}', 'show')->name('movies.show');
			Route::post('/', 'store')->name('movies.store');
			Route::patch('/{movie}', 'update')->name('movies.update');
			Route::delete('/{movie}', 'destroy')->name('movies.destroy');
		});
	});

	Route::get('/movie-genres', [GenreController::class, 'index'])->name('movies.genres');

	Route::prefix('quotes')->group(function () {
		Route::controller(QuoteController::class)->group(function () {
			Route::get('/', 'index')->name('quotes.index');
			Route::get('/{quote}', 'show')->name('quotes.show');
			Route::post('/', 'store')->name('quotes.store');
			Route::patch('/{quote}', 'update')->name('quotes.update');
			Route::delete('/{quote}', 'destroy')->name('quotes.destroy');
		});
	});

	Route::controller(LikeController::class)->group(function () {
		Route::post('/quote-like', 'store')->name('like.store');
		Route::post('/quote-destroy-like', 'destroy')->name('like.destroy');
	});

	Route::post('/comment', [CommentController::class, 'store'])->name('comment.store');

	Route::controller(NotificationController::class)->group(function () {
		Route::get('/notifications', 'index')->name('notifications.index');
		Route::get('/notifications/{notification}', 'markAsRead')->name('notifications.markAsRead');
		Route::post('/notifications/mark-all-read', 'markAllRead')->name('notifications.markAllRead');
	});
});

Broadcast::routes(['middleware' => ['auth:sanctum']]);
