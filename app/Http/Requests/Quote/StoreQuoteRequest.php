<?php

namespace App\Http\Requests\Quote;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuoteRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
	 */
	public function rules(): array
	{
		return [
			'text.en'     => ['required', 'min:3', 'max:255'],
			'text.ka'     => ['required', 'min:3', 'max:255'],
			'image'       => ['required', 'image'],
			'movie_id'    => ['required', 'integer'],
			'user_id'     => ['required'],
		];
	}

	public function prepareForValidation(): void
	{
		$this->merge([
			'user_id' => auth()->user()->id,
		]);
	}
}
