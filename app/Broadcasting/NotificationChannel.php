<?php

namespace App\Broadcasting;

use App\Models\Notification;
use App\Models\User;

class NotificationChannel
{
	/**
	 * Create a new channel instance.
	 */
	public function __construct()
	{
	}

	/**
	 * Authenticate the user's access to the channel.
	 */
	public function join(User $user, Notification $notification): array|bool
	{
		return false;
	}
}
