<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

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
			'token' => $user->createToken('Login Token of ' . $user->email)->plainTextToken,
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

		$user->sendEmailVerificationNotification();

		return $this->success([
			'user'    => $user,
			'message' => __('auth.register'),
		]);
	}

	public function logout(): JsonResponse
	{
		Auth::user()->currentAccessToken()->delete();

		return $this->success([
			'message' => __('auth.logout'),
		]);
	}
}
