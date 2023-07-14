<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
	use HasFactory;

	protected $guarded = ['id'];

	protected $with = ['quote'];

	public function notifiable(): MorphTo
	{
		return $this->morphTo();
	}

	public function quote(): BelongsTo
	{
		return $this->belongsTo(Quote::class);
	}
}
