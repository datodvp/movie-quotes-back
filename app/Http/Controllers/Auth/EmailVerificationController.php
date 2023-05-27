<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
	use HttpResponses;

	public function verify($user_id, Request $request)
	{
		if (!$request->hasValidSignature()) {
			return  $this->respondUnauthorizedRequest(253);
		}

		$user = User::findOrFail($user_id);

		if (!$user->hasVerifiedEmail()) {
			$user->markEmailAsVerified();
		}

		return response()->json('successfully verified');
	}

	public function resend()
	{
		if (auth()->user()->hasVerifiedEmail()) {
			return $this->error('', 254, 'Email already verified');
		}

		auth()->user()->sendEmailVerificationNotification();

		return $this->success('', 200, 'Verification link sent on Email');
	}
}
