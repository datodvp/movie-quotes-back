<?php

namespace App\Traits;

trait HttpResponses
{
	protected function success($data, $code = 200, $message = null)
	{
		return response()->json([
			'data'    => $data,
			'message' => $message,
		], $code);
	}

	protected function error($data, $code, $message = null)
	{
		return response()->json([
			'errors'      => $data,
			'message'     => $message,
		], $code);
	}
}
