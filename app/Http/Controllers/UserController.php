<?php

namespace App\Http\Controllers;

use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;

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
}
