<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
	 */
	public function rules(): array
	{
		return [
			'email'        => ['sometimes', 'required', 'string', 'max:255', 'email', 'unique:users,email'],
			'username'     => ['sometimes', 'string', 'min:3', 'max:15', 'regex:/^[a-z0-9]*$/', 'unique:users,username'],
			'password'     => ['sometimes', 'min:8', 'max:15', 'confirmed', 'regex:/^[a-z0-9]*$/'],
		];
	}
}
