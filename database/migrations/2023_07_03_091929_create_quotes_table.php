<?php

use App\Models\Movie;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('quotes', function (Blueprint $table) {
			$table->id();
			$table->json('text');
			$table->string('image');
			$table->foreignIdFor(Movie::class)->constrained()->cascadeOnDelete();
			$table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('quotes');
	}
};
