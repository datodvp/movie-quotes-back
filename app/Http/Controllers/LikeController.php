<?php

namespace App\Http\Controllers;

use App\Events\NotificationAdded;
use App\Events\QuoteLikeAction;
use App\Events\QuoteUnlikeAction;
use App\Http\Requests\StoreQuoteLikeRequest;
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

		// if quote is liked by NON-Author of it, SEND notification
		if ($user->id !== $quote->user->id) {
			$notification = $quote->notifiable()->create([
				'user_id'   => $quote->user->id,
				'username'  => $user->username,
				'text'      => 'Reacted to your quote',
				'is_active' => true,
			]);
			$notification['created_ago'] = $notification->created_at->diffForHumans();

			NotificationAdded::dispatch($notification->load('notifiable'));
		}

		$like = $user->likedQuotes()->find($validated['quote_id']);

		// broadcast for other users
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

		// get removed like
		$like = $user->likedQuotes()->find($validated['quote_id']);

		// detach like
		$user->likedQuotes()->detach($like);

		$quote = Quote::find($validated['quote_id']);

		// remove notification for it
		$quote->notifiable()->delete();

		// broadcast for other users
		QuoteUnlikeAction::dispatch($like);

		return $this->success([
			'message'      => 'quote unliked',
			'like'         => $like,
		]);
	}
}