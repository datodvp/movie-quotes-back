<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\CommentController;
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
	Route::get('/auth/redirect', [GoogleAuthController::class, 'redirect']);
	Route::get('/auth/callback', [GoogleAuthController::class, 'callback']);

	Route::post('/login', [AuthController::class, 'login'])->middleware('guest')->name('auth.login');
	Route::post('/register', [AuthController::class, 'register'])->middleware('guest')->name('auth.register');

	Route::post('/forgot-password', [PasswordResetController::class, 'check'])->middleware('guest')->name('password.email');
	Route::get('/reset-password/{token}', [PasswordResetController::class, 'redirect'])->middleware('guest')->name('password.reset');
	Route::post('/reset-password', [PasswordResetController::class, 'update'])->middleware('guest')->name('password.update');
});

// Protected routes

Route::group(['middleware' => ['auth:sanctum']], function () {
	Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

	Route::get('/user-data', [UserController::class, 'userData'])->name('auth.userData');
	Route::post('/change-user-credentials', [UserController::class, 'changeUserCredentials'])->name('auth.changeUserCredentials');

	Route::get('/movies', [MovieController::class, 'index'])->name('movies.index');
	Route::get('/movies/{id}', [MovieController::class, 'show'])->name('movies.show');
	Route::post('/movies', [MovieController::class, 'store'])->name('movies.store');
	Route::get('/movie-genres', [MovieController::class, 'genres'])->name('movies.genres');
	Route::get('/movies-list', [MovieController::class, 'getAllMovies'])->name('movies.list');

	Route::get('/quotes', [QuoteController::class, 'index'])->name('quotes.list');
	Route::post('/quotes', [QuoteController::class, 'store'])->name('quotes.store');
	Route::post('/quotes/search', [QuoteController::class, 'search'])->name('quotes.search');
	Route::post('/quote-like', [QuoteController::class, 'storeLike'])->name('quotes.storeLike');
	Route::post('/quote-destroy-like', [QuoteController::class, 'destroyLike'])->name('quotes.destroyLike');

	Route::post('/comment', [CommentController::class, 'store'])->name('comment.store');

	Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.list');
	Route::get('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
});

Broadcast::routes(['middleware' => ['auth:sanctum']]);
