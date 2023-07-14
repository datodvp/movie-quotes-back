<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuoteRequest;
use App\Http\Resources\QuoteResource;
use App\Models\Quote;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;

class QuoteController extends Controller
{
	use HttpResponses;

	public function index(): JsonResponse
	{
		$quotes = Quote::with(['user', 'movie',  'comments.user', 'likes'])
					->orderByDesc('created_at')
					->simplePaginate(2);

		$search = urldecode(request()->query('search'));

		if ($search) {
			$quotes = Quote::with(['user', 'movie',  'comments.user', 'likes'])->filter($search)->get();

			// if there is no tag just send empty array
			if ($search[0] !== '@' && $search[0] !== '#') {
				$quotes = [];
			}
		}

		return $this->success([
			'quotes' => QuoteResource::collection($quotes),
		]);
	}

	public function store(StoreQuoteRequest $request): JsonResponse
	{
		$validated = $request->validated();

		$validated['image'] = 'storage/' . request()->file('image')->store('images', 'public');

		$quote = Quote::create($validated);

		$quote->load('user', 'movie', 'comments.user', 'likes');

		return $this->success([
			'message' => 'movie added succesfully',
			'quote'   => new QuoteResource($quote),
		]);
	}

	public function destroy(Quote $quote): JsonResponse
	{
		$this->authorize('interact', $quote);

		if ($quote->user_id !== auth()->user()->id) {
			return $this->error('', 403, 'Your dont have permission for that!');
		}

		$quote->delete();

		return $this->success([
			'Movie have been removed!',
		]);
	}
}
