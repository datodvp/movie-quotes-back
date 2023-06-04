<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Mail\VerifyEmail;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
	use HttpResponses;

	public function login(LoginUserRequest $request): JsonResponse
	{
		$validated = $request->validated();
		// Check if "login" field is Email or Username
		$fieldName = filter_var($validated['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
		// Create field property for credentials
		$validated[$fieldName] = $validated['login'];

		if (!Auth::attempt(Arr::only($validated, [$fieldName, 'password']), isset($validated['remember']))) {
			return $this->error('', 401, __('auth.failed'));
		}

		if (!Auth::user()->hasVerifiedEmail()) {
			Auth::logout();

			return $this->error('', 403, __('auth.not_verified'));
		}

		$user = Auth::user();

		return $this->success([
			'user'  => $user,
		]);
	}

	public function register(StoreUserRequest $request): JsonResponse
	{
		$request->validated($request->all());

		$user = User::create([
			'username'     => $request->username,
			'email'        => $request->email,
			'password'     => $request->password,
		]);

		Mail::to($user)->send(new VerifyEmail($user));

		return $this->success([
			'user'    => $user,
			'message' => __('auth.register'),
		]);
	}

	public function logout(Request $request): JsonResponse
	{
		Auth::guard('web')->logout();

		$request->session()->invalidate();

		$request->session()->regenerateToken();

		return $this->success([
			'message' => __('auth.logout'),
		]);
	}

	public function checkAuthentication(Request $request): JsonResponse
	{
		if ($request->user()) {
			return $this->success([
				'message' => 'Authenticated',
			]);
		} else {
			return $this->error([
				'message' => 'Unauthenticated',
			], 401);
		}
	}
}
