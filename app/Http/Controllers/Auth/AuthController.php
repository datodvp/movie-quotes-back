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

		if (!auth()->attempt(Arr::only($validated, [$fieldName, 'password']), isset($validated['remember']))) {
			return $this->error('', 401, __('messages.authorization_failed'));
		}

		if (!auth()->user()->hasVerifiedEmail()) {
			auth()->logout();

			return $this->error('', 403, __('messages.not_verified'));
		}

		$user = auth()->user();

		return $this->success([
			'user'  => $user,
		]);
	}

	public function register(StoreUserRequest $request): JsonResponse
	{
		$validated = $request->validated();

		$user = User::create($validated);

		Mail::to($user)->send(new VerifyEmail($user));

		return $this->success([
			'user'    => $user,
			'message' => __('messages.registered_succesfully'),
		]);
	}

	public function logout(Request $request): JsonResponse
	{
		auth()->guard('web')->logout();

		$request->session()->invalidate();

		$request->session()->regenerateToken();

		return $this->success([
			'message' => __('messages.logout_succesfully'),
		]);
	}
}
