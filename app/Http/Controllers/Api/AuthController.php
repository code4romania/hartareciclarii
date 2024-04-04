<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Returns LaravelSanctum Token.
     *
     * @param Request $request
     * @requires email, password
     *
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate(
            [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]
        );

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            return response()->json(
                [
                    'user' => new UserResource($user),
                    'authorization' => [
                        'token' => $user->createToken('ApiToken')->plainTextToken,
                        'type' => 'bearer',
                    ],
                ]
            );
        }

        return response()->json(
            [
                'message' => 'invalid_credentials',
            ],
            401
        );
    }

    /**
     * Removes the authenticated user LaravelSanctum Token.
     * @param Request $request
     *
     * @requires Valid Authorization Bearer Token
     *
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::user()->tokens()->delete();

        return response()->json(
            [
                'message' => 'logout_success',
            ]
        );
    }

    /**
     * Refreshes the authenticated user LaravelSanctum Token.
     *
     * @requires Valid Authorization Bearer Token
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return response()->json(
            [
                'user' => new UserResource(Auth::user()),
                'authorisation' => [
                    'token' => Auth::refresh(),
                    'type' => 'bearer',
                ],
            ]
        );
    }
}
