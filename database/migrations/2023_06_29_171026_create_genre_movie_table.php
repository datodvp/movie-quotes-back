<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('genre_movie', function (Blueprint $table) {
			$table->unsignedBigInteger('genre_id');
			$table->unsignedBigInteger('movie_id');

			$table->foreign('genre_id')->references('id')->on('genres')->onDelete('cascade');
			$table->foreign('movie_id')->references('id')->on('movies')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('genre_movie');
	}
};
