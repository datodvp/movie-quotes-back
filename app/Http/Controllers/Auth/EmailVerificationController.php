<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
	use HttpResponses;

	public function verify(int $userId, Request $request): RedirectResponse
	{
		if (!$request->hasValidSignature()) {
			return  $this->respondUnauthorizedRequest(253);
		}

		$user = User::findOrFail($userId);

		if (!$user->hasVerifiedEmail()) {
			$user->markEmailAsVerified();
		}

		$verifiedUrl = env('FRONTEND_URL') . '/mail-verified';

		return redirect()->to($verifiedUrl);
	}
}
