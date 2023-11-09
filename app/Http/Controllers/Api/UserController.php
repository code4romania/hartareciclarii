<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
	public function register(Request $request)
	{
		dd("register");
	}
	
	/**
	 * Retrieves the authenticated user profile
	 *
	 * @requires Valid Authorization Bearer Token
	 *
	 * @return JsonResponse
	 */
	public function profile(): \Illuminate\Http\JsonResponse
	{
		return response()->json(
		[
			'data' => new UserResource(User::find(Auth::id())),
		]);
	}
}
