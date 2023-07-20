<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\Comment;
use App\Models\Notification;
use App\Traits\HttpResponses;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
	use HttpResponses;

	public function index(): JsonResponse
	{
		$notifications = Notification::where('user_id', auth()->user()->id)->get();

		$this->loadUserForCommentModel($notifications);

		return $this->success([
			'notifications' => NotificationResource::collection($notifications),
		]);
	}

	public function markAsRead(Notification $notification): JsonResponse
	{
		$notification->update([
			'is_active' => false,
		]);

		$userNotifications = Notification::where('user_id', auth()->user()->id)->get();

		$this->loadUserForCommentModel($userNotifications);

		return $this->success([
			'notifications' => NotificationResource::collection($userNotifications),
			'message'       => 'Notification succesfully updated',
		]);
	}

	public function markAllRead(): JsonResponse
	{
		Notification::where('user_id', auth()->user()->id)->update([
			'is_active' => false,
		]);

		$notifications = Notification::where('user_id', auth()->user()->id)->get();

		$this->loadUserForCommentModel($notifications);

		return $this->success([
			'notifications' => NotificationResource::collection($notifications),
			'message'       => 'Notifications marked as read succesfully',
		]);
	}

	private function loadUserForCommentModel(Collection $notifications): void
	{
		foreach ($notifications as $notification) {
			$notifiable = $notification->notifiable;

			if ($notifiable instanceof Comment) {
				$notification->notifiable->load('user');
			}
		}
	}
}
