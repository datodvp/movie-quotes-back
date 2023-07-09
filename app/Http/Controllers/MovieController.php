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

		return $this->success([
			'genres' => $genres,
		]);
	}
}
