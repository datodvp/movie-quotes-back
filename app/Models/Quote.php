<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Quote extends Model
{
	use HasFactory, HasTranslations;

	protected $guarded = ['id'];

	public $translatable = ['text'];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function comments(): HasMany
	{
		return $this->hasMany(Comment::class);
	}

	public function movie(): BelongsTo
	{
		return $this->belongsTo(Movie::class);
	}

	public function likes(): BelongsToMany
	{
		return $this->belongsToMany(User::class, 'quote_user', 'quote_id', 'user_id');
	}

	public function scopeFilter($query, $search)
	{
		if ($search) {
			if ($search[0] === '#') {
				$trimmedSearch = ltrim($search, '#');
				$query->whereRaw("json_extract(text, '$.ka') LIKE ?", ["%{$trimmedSearch}%"])
						->orWhereRaw("json_extract(text, '$.en') LIKE ?", ["%{$trimmedSearch}%"]);
			}

			if ($search[0] === '@') {
				$trimmedSearch = ltrim($search, '@');
				$query->whereHas('movie', function ($query) use ($trimmedSearch) {
					$query->whereRaw("json_extract(name, '$.ka') LIKE ?", ["%{$trimmedSearch}%"])
					->orWhereRaw("json_extract(name, '$.en') LIKE ?", ["%{$trimmedSearch}%"]);
				});
			}
		}
	}
}
