<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
	use HttpResponses;

	public function redirect()
	{
		return Socialite::driver('google')->redirect();
	}

	public function callback()
	{
		$googleUser = Socialite::driver('google')->user();

		$user = User::updateOrCreate([
			'google_id' => $googleUser->id,
		], [
			'name'  => $googleUser->name,
			'email' => $googleUser->email,
		]);

		Auth::login($user);

		return $this->success([
			'user'  => $user,
			'token' => $user->createToken('Google Login Token of ' . $user->email)->plainTextToken,
		], 201, 'User has been logged in successfully');
	}
}
