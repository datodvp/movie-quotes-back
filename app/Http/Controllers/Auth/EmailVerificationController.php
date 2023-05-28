<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
	use HttpResponses;

	public function verify($user_id, Request $request): JsonResponse
	{
		if (!$request->hasValidSignature()) {
			return  $this->respondUnauthorizedRequest(253);
		}

		$user = User::findOrFail($user_id);

		if (!$user->hasVerifiedEmail()) {
			$user->markEmailAsVerified();
		}

		return $this->success([
			'message' => 'Succesfully verified',
		]);
	}
}
