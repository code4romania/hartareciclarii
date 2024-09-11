<?php

declare(strict_types=1);

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Password;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Password::defaults(function () {
            if (app()->isLocal()) {
                return;
            }

            return Password::min(8)
                ->uncompromised()
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols();
        });

        ResetPassword::$createUrlCallback = function ($notifiable, $token) {
            return route('auth.password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);
        };

        VerifyEmail::$createUrlCallback = function ($notifiable) {
            return URL::temporarySignedRoute(
                'auth.verification.verify',
                now()->addMinutes(config('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
        };
    }
}
