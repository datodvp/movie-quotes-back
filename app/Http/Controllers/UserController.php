<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Mail\VerifyEmail;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
	use HttpResponses;

	public function index(): JsonResponse
	{
		return $this->success([
			'user' => auth()->user(),
		]);
	}

	public function update(UpdateUserRequest $request): JsonResponse
	{
		$validated = $request->validated();

		$user = auth()->user();

		if (isset($validated['email'])) {
			$user->email = $validated['email'];
			$user->email_verified_at = null;
			Mail::to($user)->queue(new VerifyEmail($user));
		}

		if (isset($validated['image'])) {
			$validated['image'] = 'storage/' . request()->file('image')->store('images', 'public');
			$user->image = $validated['image'];
		}

		$user->update($validated);
		$user->save();

		return $this->success([
			'message' => __('messages.profile_updated'),
			'user'    => $user,
		]);
	}
}
