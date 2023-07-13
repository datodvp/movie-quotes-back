<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;

class GenreController extends Controller
{
	use HttpResponses;

	public function index(): JsonResponse
	{
		return $this->success([
			'genres' => Genre::all(),
		]);
	}
}
