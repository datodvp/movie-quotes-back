<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
	use HttpResponses;

	public function login(LoginUserRequest $request): JsonResponse
	{
		$request->validated($request->all());

		$user = User::where('email', $request->email)->first();

		if (!$user->hasVerifiedEmail()) {
			return $this->error('', 403, 'Accounts Email must be verified before login');
		}

		if (!Auth::attempt($request->only('email', 'password'))) {
			return $this->error('', 401, 'Credentials do not match');
		}

		return $this->success([
			'user'  => $user,
			'token' => $user->createToken('Login Token of ' . $user->email)->plainTextToken,
		]);
	}

	public function register(StoreUserRequest $request): JsonResponse
	{
		$request->validated($request->all());

		$user = User::create([
			'name'     => $request->name,
			'email'    => $request->email,
			'password' => $request->password,
		]);

		$user->sendEmailVerificationNotification();

		return $this->success([
			'user'  => $user,
		]);
	}

	public function logout(): JsonResponse
	{
		Auth::user()->currentAccessToken()->delete();

		return $this->success([
			'message' => 'You have been successfully logged out',
		]);
	}
}
