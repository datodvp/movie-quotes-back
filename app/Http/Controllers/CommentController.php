<?php

namespace App\Http\Controllers;

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

		Comment::create($validated);

		$updatedQuote = Quote::with(['user', 'movie',  'comments.user', 'likes'])->find($validated['quote_id']);

		return $this->success([
			'message'      => 'comment added',
			'updatedQuote' => $updatedQuote,
		]);
	}
}
