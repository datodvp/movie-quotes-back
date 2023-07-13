<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
	use HttpResponses;

	public function index(): JsonResponse
	{
		// get notifications only for authorized user
		$notifications = Notification::where('user_id', auth()->user()->id)->get();

		$notifications->load('notifiable.user');

		return $this->success([
			'notifications' => $notifications,
		]);
	}

	public function markAllRead(): JsonResponse
	{
		// make every notification's is_active column false
		Notification::where('user_id', auth()->user()->id)->update([
			'is_active' => false,
		]);

		// get all notifications for current user
		$notifications = Notification::where('user_id', auth()->user()->id)->get();

		$notifications->load('notifiable.user');

		return $this->success([
			'notifications' => $notifications,
			'message'       => 'Notifications marked as read succesfully',
		]);
	}
}
