<?php

namespace App\Http\Requests\Movie;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovieRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
	 */
	public function rules(): array
	{
		return [
			'name.en'        => ['required', 'min:3', 'max:255'],
			'name.ka'        => ['required', 'min:3', 'max:255'],
			'year'           => ['required', 'numeric'],
			'director.en'    => ['required', 'min:3', 'max:255'],
			'director.ka'    => ['required', 'min:3', 'max:255'],
			'description.en' => ['required', 'min:3'],
			'description.ka' => ['required', 'min:3'],
			'genres'         => ['required', 'array'],
			'image'          => ['required', 'image'],
			'user_id'        => ['required'],
		];
	}

	public function prepareForValidation(): void
	{
		$this->merge([
			'user_id' => auth()->user()->id,
			'genres'  => json_decode($this->genres, true),
		]);
	}
}
