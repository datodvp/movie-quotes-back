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
		$notifications = Notification::where('user_id', auth()->user()->id)->get();

		foreach ($notifications as $notification) {
			$notification->created_ago = $notification->created_at->diffForHumans();
			$notification->notifiable->user;
		}

		return $this->success([
			'notifications' => $notifications,
		]);
	}

	public function markAllRead(): JsonResponse
	{
		Notification::where('user_id', auth()->user()->id)->update([
			'is_active' => false,
		]);

		$notifications = Notification::where('user_id', auth()->user()->id)->get();

		foreach ($notifications as $notification) {
			$notification->created_ago = $notification->created_at->diffForHumans();
			$notification->notifiable->user;
		}

		return $this->success([
			'notifications' => $notifications,
			'message'       => 'Notifications marked as read succesfully',
		]);
	}
}
