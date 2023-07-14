<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\Comment;
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

		// We have to check if notifiable is Comment model or User model
		// because we dont have Like model we have to do this check manually
		// as we want to fetch 'user' relationship for Comment model but not for User model
		foreach ($notifications as $notification) {
			$notifiable = $notification->notifiable;

			if ($notifiable instanceof Comment) {
				$notification->notifiable->load('user');
			}
		}

		return $this->success([
			'notifications' => NotificationResource::collection($notifications),
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

		foreach ($notifications as $notification) {
			$notifiable = $notification->notifiable;

			if ($notifiable instanceof Comment) {
				$notification->notifiable->load('user');
			}
		}

		return $this->success([
			'notifications' => NotificationResource::collection($notifications),
			'message'       => 'Notifications marked as read succesfully',
		]);
	}
}
