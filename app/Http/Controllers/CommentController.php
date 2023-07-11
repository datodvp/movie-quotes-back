<?php

namespace App\Http\Controllers;

use App\Events\NotificationAdded;
use App\Events\QuoteCommented;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
	use HttpResponses;

	public function store(StoreCommentRequest $request): JsonResponse
	{
		$validated = $request->validated();

		$validated['user_id'] = auth()->user()->id;

		$comment = Comment::create($validated);

		$notification = [];

		// if commenter is not the post author, send notification
		if ($comment->user->id !== $comment->quote->user->id) {
			$notification = $comment->notifications()->create([
				'user_id'   => $comment->quote->user->id,
				'username'  => $comment->user->username,
				'text'      => 'Commented to your movie quote',
				'is_active' => true,
			]);

			$notification['created_ago'] = $notification->created_at->diffForHumans();
			NotificationAdded::dispatch($notification->load('notifiable.user'));
		}

		$comment->unsetRelation('quote');

		QuoteCommented::dispatch($comment);

		return $this->success([
			'message'      => 'comment added',
			'comment'      => $comment,
		]);
	}
}
