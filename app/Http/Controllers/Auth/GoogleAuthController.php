<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;

class GoogleAuthController extends Controller
{
	use HttpResponses;

	public function redirect(): Response
	{
		return Socialite::driver('google')->redirect();
	}

	public function callback(): UrlGenerator|string
	{
		$googleUser = Socialite::driver('google')->user();

		$user = User::updateOrCreate([
			'google_id' => $googleUser->id,
		], [
			'username'  => $googleUser->name,
			'email'     => $googleUser->email,
		]);

		Auth::login($user);

		return redirect(env('SPA_URL'));
	}
}
