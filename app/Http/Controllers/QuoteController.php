<?php

namespace App\Http\Controllers;

use App\Events\QuoteLikeAction;
use App\Events\QuoteUnlikeAction;
use App\Http\Requests\StoreQuoteLikeRequest;
use App\Http\Requests\StoreQuoteRequest;
use App\Models\Quote;
use App\Traits\HttpResponses;

class QuoteController extends Controller
{
	use HttpResponses;

	public function index()
	{
		$quotes = Quote::with(['user', 'movie',  'comments.user', 'likes'])->orderByDesc('created_at')->get();

		return $this->success([
			'quotes' => $quotes,
		]);
	}

	public function store(StoreQuoteRequest $request)
	{
		$validated = $request->validated();

		$validated['user_id'] = auth()->user()->id;
		$validated['image'] = 'storage/' . request()->file('image')->store('images', 'public');

		$quote = Quote::create($validated);

		$quote->load('user', 'movie', 'comments.user', 'likes');

		return $this->success([
			'message' => 'movie added succesfully',
			'quote'   => $quote,
		]);
	}

	public function storeLike(StoreQuoteLikeRequest $request)
	{
		$validated = $request->validated();

		$user = auth()->user();

		$user->likedQuotes()->attach($validated);

		$quote = Quote::with(['user', 'movie',  'comments.user', 'likes'])->find($validated['quote_id']);

		// if user liked his own quote dont notify
		if ($user->id !== $quote->user->id) {
			$user->likesNotifiable()->create([
				'user_id'   => $quote->user->id,
				'username'  => $user->username,
				'text'      => 'Reacted to your quote',
				'is_active' => true,
			]);
		}

		$like = $user->likedQuotes()->find($validated['quote_id']);

		$pivot = $like->pivot;

		QuoteLikeAction::dispatch($like);

		return $this->success([
			'message'      => 'like added',
			'like'         => $like,
		]);
	}

	public function destroyLike(StoreQuoteLikeRequest $request)
	{
		$validated = $request->validated();

		$user = auth()->user();

		$like = $user->likedQuotes()->find($validated['quote_id']);

		$user->likedQuotes()->detach($validated);

		$user->likesNotifiable()->delete();

		QuoteUnlikeAction::dispatch($like);

		return $this->success([
			'message'      => 'quote unliked',
			'like'         => $like,
		]);
	}
}
