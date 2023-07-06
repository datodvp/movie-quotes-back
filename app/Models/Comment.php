<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Comment extends Model
{
	use HasFactory;

	protected $guarded = ['id'];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function quote()
	{
		return $this->belongsTo(Quote::class);
	}

	public function notifications(): MorphMany
	{
		return $this->morphMany(Notification::class, 'notifiable');
	}
}
