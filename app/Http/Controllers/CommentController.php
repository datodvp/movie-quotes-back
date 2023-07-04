<?php

namespace App\Http\Controllers;

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

		$comment->load('quote.user', 'quote.comments.user', 'quote.movie');

		return $this->success([
			'newComment' => $comment,
		]);
	}
}
