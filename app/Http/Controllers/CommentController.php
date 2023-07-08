<?php

namespace App\Http\Controllers;

use App\Events\QuoteCommented;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Quote;
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

		// if commenter himself is quote author DONT notify
		if ($comment->user->id !== $comment->quote->user->id) {
			$comment->notifications()->create([
				'user_id'   => $comment->quote->user->id,
				'username'  => $comment->user->username,
				'text'      => 'Commented to your movie quote',
				'is_active' => true,
			]);
		}

		$quote = Quote::with(['user', 'movie',  'comments.user', 'likes'])->find($validated['quote_id']);

		QuoteCommented::dispatch($quote);

		return $this->success([
			'message'      => 'comment added',
			'updatedQuote' => $quote,
		]);
	}
}
