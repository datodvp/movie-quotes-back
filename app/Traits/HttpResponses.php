<?php

namespace App\Traits;

trait HttpResponses
{
	protected function success($data, $code = 200, $message = null)
	{
		return response()->json([
			'status'  => 'Request was successful',
			'message' => $message,
			'data'    => $data,
		], $code);
	}

	protected function error($data, $code, $message = null)
	{
		return response()->json([
			'status'  => 'Error has occured...',
			'message' => $message,
			'data'    => $data,
		], $code);
	}
}
