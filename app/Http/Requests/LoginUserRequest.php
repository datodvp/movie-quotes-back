<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginUserRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
	 */
	public function rules(): array
	{
		return [
			'login'       => ['required', 'string', 'min:3', 'max:255'],
			'password'    => ['required', 'string', 'min:8', 'max:255'],
		];
	}
}
