<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Models\Genre;
use App\Models\Movie;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;

class MovieController extends Controller
{
	use HttpResponses;

	public function index()
	{
		// get only movies created by authorized user
		$movies = Movie::where('user_id', auth()->user()->id)->get();

		$movies->load('quotes');

		return $this->success([
			'movies' => $movies,
		]);
	}

	public function search(): JsonResponse
	{
		$query = request()->input('search');

		// search movies only created by authorized user
		$movies = Movie::where('user_id', auth()->user()->id)
		->where(function ($dbQuery) use ($query) {
			$dbQuery->whereRaw("json_extract(name, '$.ka') LIKE ?", ["%{$query}%"])
					->orWhereRaw("json_extract(name, '$.en') LIKE ?", ["%{$query}%"]);
		})
		->get();

		$movies->load('quotes');

		return $this->success([
			'movies' => $movies,
		]);
	}

	public function store(StoreMovieRequest $request): JsonResponse
	{
		$validated = $request->validated();

		$validated['image'] = 'storage/' . request()->file('image')->store('images', 'public');

		$movie = Movie::create($validated);

		// attach genres to movie
		$movie->genres()->sync($validated['genres']);

		$movie->load('quotes.comments.user', 'quotes.likes', 'genres');

		return response()->json([
			'message' => 'movie added succesfully',
			'movie'   => $movie,
		]);
	}

	public function show(string $id): JsonResponse
	{
		$movie = Movie::find($id);

		if (!$movie) {
			return $this->error('', 404, 'Movie not found');
		}

		if ($movie->user_id !== auth()->user()->id) {
			return $this->error('', 403, 'you are forbidden from accessing this page');
		}

		$movie->load('quotes.comments.user', 'quotes.likes', 'genres');

		return $this->success([
			'movie' => $movie,
		]);
	}

	public function update(UpdateMovieRequest $request, string $id): JsonResponse
	{
		$validated = $request->validated();

		$movie = Movie::find($id);

		$movie->update($validated);

		// first detach genres to reSet them
		$movie->genres()->detach();

		$movie->genres()->attach($validated['genres']);

		if (isset($validated['image'])) {
			$validated['image'] = 'storage/' . request()->file('image')->store('images', 'public');
			$movie->image = $validated['image'];
		}

		$movie->save();

		$movie->load('quotes.comments', 'quotes.likes', 'genres');

		return $this->success([
			'message' => 'Movie has been changed!',
			'movie'   => $movie,
		]);
	}

	public function destroy(string $id): JsonResponse
	{
		$movie = Movie::find($id);

		// check if delete request if from author of the movie
		if ($movie->user_id !== auth()->user()->id) {
			return $this->error('', 403, 'you cant do that!');
		}

		$movie->delete();

		return $this->success([
			'Movie have been removed!',
		]);
	}

	public function genres(): JsonResponse
	{
		$genres = Genre::all();

		return $this->success([
			'genres' => $genres,
		]);
	}
}
