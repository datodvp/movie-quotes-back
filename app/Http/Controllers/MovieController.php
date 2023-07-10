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

	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
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

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(StoreMovieRequest $request)
	{
		$validated = $request->validated();

		$validated['user_id'] = auth()->user()->id;
		$validated['genres'] = json_decode($validated['genres'], true);
		$validated['image'] = 'storage/' . request()->file('image')->store('images', 'public');

		$movie = Movie::create($validated);

		// attach genres to movie
		foreach ($validated['genres'] as $genre) {
			$movie->genres()->attach($genre['id']);
		}

		return response()->json([
			'message' => 'movie added succesfully',
			'movie'   => $movie,
		]);
	}

	public function getAllMovies(): JsonResponse
	{
		$movies = Movie::all();

		return $this->success([
			'movies' => $movies,
		]);
	}

	/**
	 * Display the specified resource.
	 */
	public function show(string $id): JsonResponse
	{
		$movie = Movie::find($id)->load('quotes.comments', 'quotes.likes', 'genres');

		if ($movie->user_id !== auth()->user()->id) {
			return $this->error('', 403, 'you are forbidden from accessing this page');
		}

		return $this->success([
			'movie' => $movie,
		]);
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function update(UpdateMovieRequest $request, string $id)
	{
		$validated = $request->validated();
		$validated['genres'] = json_decode($validated['genres'], true);

		$movie = Movie::find($id);

		$movie->name = $validated['name'];
		$movie->year = $validated['year'];
		$movie->director = $validated['director'];
		$movie->description = $validated['description'];

		// first detach genres to reSet them
		$movie->genres()->detach();

		foreach ($validated['genres'] as $genre) {
			$movie->genres()->attach($genre['id']);
		}

		if (isset($validated['image'])) {
			$validated['image'] = 'storage/' . request()->file('image')->store('images', 'public');
			$movie->image = $validated['image'];
			$movie->save();
		}

		$movie->save();

		$movie->load('quotes.comments', 'quotes.likes', 'genres');

		return $this->success([
			'message' => 'Movie has been changed!',
			'movie'   => $movie,
		]);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(string $id)
	{
		$movie = Movie::find($id);
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
