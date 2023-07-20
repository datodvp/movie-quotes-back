<?php

namespace App\Http\Controllers;

use App\Events\NotificationAdded;
use App\Events\QuoteLikeAction;
use App\Events\QuoteUnlikeAction;
use App\Http\Requests\Quote\StoreQuoteLikeRequest;
use App\Models\Quote;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;

class LikeController extends Controller
{
	use HttpResponses;

	public function store(StoreQuoteLikeRequest $request): JsonResponse
	{
		$validated = $request->validated();

		$user = auth()->user();

		$user->likedQuotes()->attach($validated);

		$quote = Quote::with(['user', 'movie',  'comments.user', 'likes'])->find($validated['quote_id']);

		if ($user->id !== $quote->user->id) {
			$notification = $user->likeNotifiable()->create([
				'user_id'   => $quote->user->id,
				'quote_id'  => $quote->id,
				'username'  => $user->username,
				'text'      => 'Reacted to your quote',
				'is_active' => true,
			]);
			$notification['created_ago'] = $notification->created_at->diffForHumans();

			NotificationAdded::dispatch($notification->load('notifiable', 'quote'));
		}

		$like = $user->likedQuotes()->find($validated['quote_id']);

		QuoteLikeAction::dispatch($like);

		return $this->success([
			'message'      => 'like added',
			'like'         => $like,
		]);
	}

	public function destroy(StoreQuoteLikeRequest $request): JsonResponse
	{
		$validated = $request->validated();

		$user = auth()->user();

		$like = $user->likedQuotes()->find($validated['quote_id']);

		$user->likedQuotes()->detach($like);

		$user->likeNotifiable()->delete();

		QuoteUnlikeAction::dispatch($like);

		return $this->success([
			'message'      => 'quote unliked',
			'like'         => $like,
		]);
	}
}
