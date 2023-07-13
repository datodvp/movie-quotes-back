<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Movie extends Model
{
	use HasFactory, HasTranslations;

	protected $guarded = ['id'];

	protected $with = ['quotes'];

	// protected $with = ['genres', 'quotes'];

	public $translatable = ['name', 'director', 'description'];

	public function genres(): BelongsToMany
	{
		return $this->belongsToMany(Genre::class);
	}

	public function quotes(): HasMany
	{
		return $this->HasMany(Quote::class);
	}

	public function scopeFilter($query, $search)
	{
		$query->where('user_id', auth()->user()->id)
		->where(function ($dbQuery) use ($search) {
			$dbQuery->whereRaw("json_extract(name, '$.ka') LIKE ?", ["%{$search}%"])
					->orWhereRaw("json_extract(name, '$.en') LIKE ?", ["%{$search}%"]);
		});
	}
}
