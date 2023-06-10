<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;

class GoogleAuthController extends Controller
{
	use HttpResponses;

	public function redirect(): Response
	{
		return Socialite::driver('google')->redirect();
	}

	public function callback(): RedirectResponse
	{
		$googleUser = Socialite::driver('google')->user();

		$user = User::updateOrCreate([
			'google_id' => $googleUser->id,
		], [
			'username'  => $googleUser->name,
			'email'     => $googleUser->email,
		]);

		auth()->login($user);

		$redirectUrl = env('FRONTEND_URL') . '?token=' . $googleUser->token;

		return redirect()->to($redirectUrl);
	}
}
