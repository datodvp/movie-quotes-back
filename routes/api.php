<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
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

// Public routes

Route::post('/login', [AuthController::class, 'login'])->middleware('guest')->name('auth.login');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest')->name('auth.register');

Route::get('/email/verify/{id}', [EmailVerificationController::class, 'verify'])->middleware('guest')->name('verification.verify');

Route::post('/forgot-password', [PasswordResetController::class, 'check'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'redirect'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'update'])->middleware('guest')->name('password.update');

// Protected routes

Route::group(['middleware' => ['auth:sanctum']], function () {
	Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('auth.logout');
});
