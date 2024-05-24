<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    /**
     * Handle an incoming authentication request.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::getDashboardUrl());
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $user = User::create(
            [
                'firstname' => $data['first_name'],
                'lastname' => $data['last_name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'accept_terms' => $data['accept_terms'],
                'send_newsletter' => $data['send_newsletter'] ?? false,
            ]
        );
        $user->notify(new WelcomeNotification());
        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->intended(RouteServiceProvider::getDashboardUrl());

    }

    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
