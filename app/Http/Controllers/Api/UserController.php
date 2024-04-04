<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Http\Resources\UserResource;
    use App\Models\User;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Password;
    use Illuminate\Validation\Rules\Password as PasswordValidator;

    class UserController extends Controller
    {
        public function register(Request $request)
        {
            $this->validate($request, [
                'email' => [
                    'required',
                    'email',
                    'unique:users,email',
                ],
                'firstname' => [
                    'required',
                    'string',
                ],
                'lastname' => [
                    'required',
                    'string',
                ],
                'password' => [
                    'required',
                    PasswordValidator::min(8)->mixedCase(),
                ],
            ]);

            $user = User::createFromArray($request->all());

            return response()->json(
                [
                    'data' => new UserResource($user),
                ]
            );
        }

        /**
         * Retrieves the authenticated user profile.
         *
         * @requires Valid Authorization Bearer Token
         *
         * @return JsonResponse
         */
        public function profile(): JsonResponse
        {
            return response()->json(
                [
                    'data' => new UserResource(User::find(Auth::id())),
                ]
            );
        }

        public function recoverPassword(Request $request)
        {
            $this->validate($request, [
                'email' => [
                    'required',
                    'email',
                    'exists:users,email',
                ],
            ]);

            $status = Password::sendResetLink(
                $request->only('email')
            );

            return response()->json(
                [
                    'status' => Password::RESET_LINK_SENT,
                ]
            );
        }

        public function recoverPasswordConfirm(Request $request)
        {
            $status = Password::reset(
                $request->only('password', 'password_confirmation', 'token'),
                function (User $user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                    ])->setRememberToken(Str::random(60));

                    $user->save();
                }
            );

            return response()->json(
                [
                    'status' => $status,
                ]
            );
        }

        public function sendPasswordResetLink(Request $request)
        {
            return $this->sendResetLinkEmail($request);
        }

        protected function sendResetLinkResponse(Request $request, $response)
        {
            return response()->json([
                'message' => 'Password reset email sent.',
                'data' => $response,
            ]);
        }

        protected function sendResetLinkFailedResponse(Request $request, $response)
        {
            return response()->json(['message' => 'Email could not be sent to this email address.']);
        }

        protected function sendResetResponse(Request $request, $response)
        {
            return response()->json(['message' => 'Password reset successfully.']);
        }

        protected function sendResetFailedResponse(Request $request, $response)
        {
            return response()->json(['message' => 'Failed, Invalid Token.']);
        }
    }
