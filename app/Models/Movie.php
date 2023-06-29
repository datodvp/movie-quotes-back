<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Movie extends Model
{
	use HasFactory, HasTranslations;

	protected $guarded = ['id'];

	public $translatable = ['name', 'director', 'description'];

	public function genres()
	{
		return $this->belongsToMany(Genre::class);
	}
}
