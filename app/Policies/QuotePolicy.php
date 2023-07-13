<?php

namespace App\Policies;

use App\Models\Quote;
use App\Models\User;

class QuotePolicy
{
	public function interact(User $user, Quote $quote): bool
	{
		return $user->id == $quote->user_id;
	}
}
