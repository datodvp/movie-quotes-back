<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckEmailRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Mail\PasswordReset as MailPasswordReset;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
	use HttpResponses;

	public function check(CheckEmailRequest $request): JsonResponse
	{
		$validated = $request->validated();

		$user = User::where('email', $validated)->first();

		if (!$user) {
			return $this->error([
				'message' => __('auth.reset_link_not_sent'),
			], 401);
		}

		$token = app('auth.password.broker')->createToken($user);

		Mail::to($validated)->send(new MailPasswordReset($user, $token));

		return $this->success([
			'message' => __('auth.reset_link_sent'),
		]);
	}

	public function redirect($token, Request $request): RedirectResponse
	{
		$email = $request->query('email');

		$resetPasswordUrl = env('FRONTEND_URL') . '/reset-password' . '?token=' . $token . '&email=' . $email;

		return redirect()->to($resetPasswordUrl);
	}

	public function update(PasswordResetRequest $request): JsonResponse
	{
		$validated = $request->validated();

		$status = Password::reset(
			$validated,
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
						'message' => __('auth.password_updated'),
						'status'  => $status,
					]) : $this->error([
						'message' => __('auth.password_not_updated'),
						'status'  => $status,
					], 401);
	}
}
