<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMovieRequest;
use App\Models\Genre;
use App\Models\Movie;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MovieController extends Controller
{
	use HttpResponses;

	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		$movies = Movie::where('user_id', auth()->user()->id)->get();

		// this map method translates movies with spatie
		$translatedMovies = $movies->map(function ($movie) {
			return [
				'name'        => $movie->name,
				'year'        => $movie->year,
			];
		});

		return $this->success([
			'movies' => $translatedMovies,
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(StoreMovieRequest $request)
	{
		$validated = $request->validated();

		$validated['user_id'] = auth()->user()->id;

		$movie = Movie::create($validated);

		$translatedMovie = [
			'name'        => $movie->name,
			'year'        => $movie->year,
			'director'    => $movie->director,
			'description' => $movie->description,
		];

		// add genres to the movie
		foreach ($validated['genres'] as $genre) {
			$movie->genres()->attach($genre['id']);
		}

		return response()->json([
			'message' => 'movie added succesfully',
			'movie'   => $translatedMovie,
		]);
	}

	/**
	 * Display the specified resource.
	 */
	public function show(string $id)
	{
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(string $id)
	{
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, string $id)
	{
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(string $id)
	{
	}

	public function genres(): JsonResponse
	{
		$genres = Genre::all();

		$translatedGenres = $genres->map(function ($genre) {
			return [
				'id'   => $genre->id,
				'name' => $genre->name,
			];
		});

		return $this->success([
			'genres' => $translatedGenres,
		]);
	}
}
