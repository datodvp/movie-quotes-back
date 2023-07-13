<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuoteRequest;
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

		return $this->success([
			'quotes' => $quotes,
		]);
	}

	public function search(): JsonResponse
	{
		$query = request()->input('search');

		if ($query[0] === '#') {
			$search = ltrim($query, '#');
			$quotes = Quote::with(['user', 'movie',  'comments.user', 'likes'])
					->whereRaw("json_extract(text, '$.ka') LIKE ?", ["%{$search}%"])
					->orWhereRaw("json_extract(text, '$.en') LIKE ?", ["%{$search}%"])
					->get();
		}

		if ($query[0] === '@') {
			$search = ltrim($query, '@');
			$quotes = Quote::with(['user', 'movie',  'comments.user', 'likes'])
			->whereHas('movie', function ($query) use ($search) {
				$query->whereRaw("json_extract(name, '$.ka') LIKE ?", ["%{$search}%"])
				->orWhereRaw("json_extract(name, '$.en') LIKE ?", ["%{$search}%"]);
			})
			->get();
		}

		// if there is no tag just send empty array
		if ($query[0] !== '@' && $query[0] !== '#') {
			$quotes = [];
		}

		return $this->success([
			'quotes' => $quotes,
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
			'quote'   => $quote,
		]);
	}

	public function destroy(string $id): JsonResponse
	{
		$quote = Quote::find($id);

		if ($quote->user_id !== auth()->user()->id) {
			return $this->error('', 403, 'Your dont have permission for that!');
		}

		$quote->delete();

		return $this->success([
			'Movie have been removed!',
		]);
	}
}
