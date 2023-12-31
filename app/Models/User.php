<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail, CanResetPasswordContract
{
	use HasApiTokens, HasFactory, Notifiable, CanResetPassword;

	protected $with = ['likedQuotes'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	public function likedQuotes(): BelongsToMany
	{
		return $this->belongsToMany(Quote::class, 'quote_user', 'user_id', 'quote_id');
	}

	public function notifications(): HasMany
	{
		return $this->hasMany(Notification::class);
	}

	public function likeNotifiable(): MorphMany
	{
		return $this->morphMany(Notification::class, 'notifiable');
	}

	protected $fillable = [
		'username',
		'email',
		'password',
		'google_id',
	];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * The attributes that should be cast.
	 *
	 * @var array<string, string>
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
		'password'          => 'hashed',
	];
}
