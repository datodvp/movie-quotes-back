<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMovieRequest;
use App\Http\Requests\UpdateMovieRequest;
use App\Http\Resources\MovieResource;
use App\Models\Movie;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;

class MovieController extends Controller
{
	use HttpResponses;

	public function index(): JsonResponse
	{
		// get only movies created by authorized user
		$movies = Movie::where('user_id', auth()->user()->id)->get();

		if ($search = request()->query('search')) {
			$movies = Movie::filter($search)->get();
		}

		// $movies->load('quotes');

		return $this->success([
			'movies' => MovieResource::collection($movies),
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
			'movie'   => new MovieResource($movie),
		]);
	}

	public function show(Movie $movie): JsonResponse
	{
		$this->authorize('interact', $movie);

		$movie->load('quotes.comments.user', 'quotes.likes', 'genres');

		return $this->success([
			'movie' => new MovieResource($movie),
		]);
	}

	public function update(UpdateMovieRequest $request, Movie $movie): JsonResponse
	{
		$this->authorize('interact', $movie);

		$validated = $request->validated();

		$movie->update($validated);

		// first detach genres to reSet them
		$movie->genres()->detach();

		$movie->genres()->attach($validated['genres']);

		if (isset($validated['image'])) {
			$validated['image'] = 'storage/' . request()->file('image')->store('images', 'public');
			$movie->update([
				'image' => $validated['image'],
			]);
		}

		$movie->save();

		return $this->success([
			'message' => 'Movie has been changed!',
			'movie'   => new MovieResource($movie),
		]);
	}

	public function destroy(Movie $movie): JsonResponse
	{
		// check if delete request if from author of the movie
		$this->authorize('interact', $movie);

		$movie->delete();

		return $this->success([
			'Movie have been removed!',
		]);
	}
}
