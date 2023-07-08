<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Mail\VerifyEmail;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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

		$user = auth()->user();

		if (isset($validated['email'])) {
			$user->update([
				'email'             => $validated['email'],
			]);
			$user->email_verified_at = null;
			$user->save();

			Mail::to($user)->queue(new VerifyEmail($user));
		}

		if (isset($validated['image'])) {
			$validated['image'] = 'storage/' . request()->file('image')->store('images', 'public');
			$user->image = $validated['image'];
			$user->save();
		}

		if (isset($validated['username'])) {
			$user->update([
				'username'          => $validated['username'],
			]);
		}

		if (isset($validated['password'])) {
			$user->update([
				'password' => Hash::make($validated['password']),
			]);
		}

		return $this->success([
			'message' => __('messages.profile_updated'),
			'user'    => $user,
		]);
	}
}
