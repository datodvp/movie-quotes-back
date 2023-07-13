<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
	 */
	public function rules(): array
	{
		return [
			'text'     => ['required', 'max:1000'],
			'quote_id' => ['required', 'integer'],
			'user_id'  => ['required'],
		];
	}

	protected function prepareForValidation(): void
	{
		$this->merge([
			'user_id' => auth()->user()->id,
		]);
	}
}
