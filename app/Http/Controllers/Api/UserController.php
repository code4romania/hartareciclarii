<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
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
	
	public function recoverPassword(Request $request)
	{
		$this->validate($request, [
			'email' => [
				'required',
				'email',
				'exists:users,email',
			]
		]);
		
		$status = Password::sendResetLink(
			$request->only('email')
		);
		dd($status);
		return $status === Password::RESET_LINK_SENT
			? back()->with(['status' => __($status)])
			: back()->withErrors(['email' => __($status)]);
		
	}
}
