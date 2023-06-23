<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
	use HttpResponses;

	public function userData(): JsonResponse
	{
		$user = auth()->user();
		return $this->success([
			'user' => $user,
		]);
	}

	public function changeUserCredentials(ChangePasswordRequest $request): JsonResponse
	{
		$validated = $request->validated();

		if (isset($validated['username'])) {
			auth()->user()->update([
				'username' => $validated['username'],
			]);
		}

		if (isset($validated['password'])) {
			auth()->user()->update([
				'password' => Hash::make($validated['password']),
			]);
		}

		return $this->success([
			'message' => __('auth.password_updated'),
		]);
	}
}
