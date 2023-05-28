<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
	 */
	public function rules(): array
	{
		return [
			'username'     => ['required', 'string', 'min:3', 'max:15', 'regex:/^[a-z0-9]*$/'],
			'email'        => ['required', 'string', 'max:255', 'email', 'unique:users'],
			'password'     => ['required', 'confirmed', 'min:8', 'max:15', 'regex:/^[a-z0-9]*$/'],
		];
	}
}
