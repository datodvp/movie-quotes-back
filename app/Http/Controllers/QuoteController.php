<?php

namespace App\Http\Controllers;

use App\Events\QuoteLikeAction;
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

		$updatedQuote = Quote::with(['user', 'movie',  'comments.user', 'likes'])->find($validated['quote_id']);

		QuoteLikeAction::dispatch($updatedQuote);

		return $this->success([
			'message'      => 'like added',
			'updatedQuote' => $updatedQuote,
		]);
	}

	public function destroyLike(StoreQuoteLikeRequest $request)
	{
		$validated = $request->validated();

		$user = auth()->user();

		$user->likedQuotes()->detach($validated);

		$updatedQuote = Quote::with(['user', 'movie',  'comments.user', 'likes'])->find($validated['quote_id']);

		QuoteLikeAction::dispatch($updatedQuote);

		return $this->success([
			'message'      => 'quote unliked',
			'updatedQuote' => $updatedQuote,
		]);
	}
}
