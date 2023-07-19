<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class GenreSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$json = File::get('database/seeders/genres/genres.json');
		$genres = json_decode($json);

		foreach ($genres as $genre) {
			Genre::create([
				'name' => [
					'en'=> $genre->en,
					'ka'=> $genre->ka,
				],
			]);
		}
	}
}
