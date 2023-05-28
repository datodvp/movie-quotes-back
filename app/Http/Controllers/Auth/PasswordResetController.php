<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckEmailRequest;
use App\Traits\HttpResponses;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
	use HttpResponses;

	public function check(CheckEmailRequest $request): JsonResponse
	{
		$request->validated();

		$status = Password::sendResetLink(
			$request->only('email')
		);

		return $status === Password::RESET_LINK_SENT ? $this->success([
			'message' => 'Reset link sent succesfully',
			'status'  => $status,
		]) : $this->error([
			'message' => 'Unfortunately reset link could not be sent',
			'status'  => $status,
		], 401);
	}

	public function redirect($token): JsonResponse
	{
		return $this->success([
			'message' => 'Succesful redirect to password change page',
			'token'   => $token,
		]);
	}

	public function update(Request $request): JsonResponse
	{
		$request->validate([
			'token'    => 'required',
			'email'    => 'required|email',
			'password' => 'required|min:8|confirmed',
		]);

		$status = Password::reset(
			$request->only('email', 'password', 'password_confirmation', 'token'),
			function ($user, $password) {
				$user->forceFill([
					'password' => Hash::make($password),
				]);

				$user->save();

				event(new PasswordReset($user));
			}
		);

		return $status === Password::PASSWORD_RESET
					? $this->success([
						'message' => 'Password has been updated',
						'status'  => $status,
					]) : $this->error([
						'message' => 'Could not update a password sorry...',
						'status'  => $status,
					], 401);
	}
}
