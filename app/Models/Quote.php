<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Translatable\HasTranslations;

class Quote extends Model
{
	use HasFactory, HasTranslations;

	protected $guarded = ['id'];

	public $translatable = ['text'];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function comments()
	{
		return $this->hasMany(Comment::class);
	}

	public function movie()
	{
		return $this->belongsTo(Movie::class);
	}

	public function likes()
	{
		return $this->belongsToMany(User::class, 'quote_user', 'quote_id', 'user_id');
	}

	public function notifications(): MorphMany
	{
		return $this->morphMany(Notification::class, 'notifiable');
	}
}
