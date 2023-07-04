<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuoteRequest;
use App\Models\Quote;
use App\Traits\HttpResponses;

class QuoteController extends Controller
{
	use HttpResponses;

	public function index()
	{
		$quotes = Quote::with(['user', 'movie',  'comments.user'])->orderByDesc('created_at')->get();

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

		$quote->load('user', 'movie', 'comments.user');

		return $this->success([
			'message' => 'movie added succesfully',
			'quote'   => $quote,
		]);
	}
}
