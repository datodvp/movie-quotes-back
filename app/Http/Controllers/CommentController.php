<?php

namespace App\Http\Controllers;

use App\Events\NotificationAdded;
use App\Events\QuoteCommented;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
	use HttpResponses;

	public function store(StoreCommentRequest $request): JsonResponse
	{
		$validated = $request->validated();

		$comment = Comment::create($validated);

		$notification = [];

		if ($comment->user->id !== $comment->quote->user->id) {
			$notification = $comment->notifications()->create([
				'user_id'   => $comment->quote->user->id,
				'quote_id'  => $comment->quote->id,
				'username'  => $comment->user->username,
				'text'      => 'Commented to your movie quote',
				'is_active' => true,
			]);
			NotificationAdded::dispatch($notification->load('notifiable.user', 'quote'));
		}

		$comment->unsetRelation('quote');

		QuoteCommented::dispatch($comment);

		return $this->success([
			'message'      => 'comment added',
			'comment'      => new CommentResource($comment),
		]);
	}
}
