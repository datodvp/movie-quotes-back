<?php

namespace App\Http\Controllers;

use App\Http\Requests\Quote\StoreQuoteRequest;
use App\Http\Requests\Quote\UpdateQuoteRequest;
use App\Http\Resources\QuoteResource;
use App\Models\Quote;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;

class QuoteController extends Controller
{
	use HttpResponses;

	public function index(): JsonResponse
	{
		$search = urldecode(request()->query('search'));

		$quotes = Quote::with(['user', 'movie',  'comments.user', 'likes'])
					->filter($search)
					->orderByDesc('created_at')
					->simplePaginate(5);

		return $this->success([
			'quotes' => QuoteResource::collection($quotes),
		]);
	}

	public function show(Quote $quote): JsonResponse
	{
		$quote->load('comments.user', 'likes', 'user', 'movie');
		return $this->success([
			'quote' => new QuoteResource($quote),
		]);
	}

	public function store(StoreQuoteRequest $request): JsonResponse
	{
		$validated = $request->validated();

		$validated['image'] = url('storage/' . request()->file('image')->store('images', 'public'));

		$quote = Quote::create($validated);

		$quote->load('user', 'movie', 'comments.user', 'likes');

		return $this->success([
			'message' => 'movie added succesfully',
			'quote'   => new QuoteResource($quote),
		]);
	}

	public function update(UpdateQuoteRequest $request, Quote $quote): JsonResponse
	{
		$this->authorize('interact', $quote);

		$validated = $request->validated();

		if (isset($validated['image'])) {
			$validated['image'] = url('storage/' . request()->file('image')->store('images', 'public'));
		}

		$quote->update($validated);

		$quote->load('comments.user', 'likes');

		return $this->success([
			'message' => 'Movie has been changed!',
			'movie'   => new QuoteResource($quote),
		]);
	}

	public function destroy(Quote $quote): JsonResponse
	{
		$this->authorize('interact', $quote);

		$quote->delete();

		return $this->success([
			'Movie have been removed!',
		]);
	}
}
