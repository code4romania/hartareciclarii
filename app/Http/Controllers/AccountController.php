<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function dashboard(): Response
    {
        return Inertia::render('Account/Dashboard', [
            'contributions_count' => rand(1, 1234),

            'contributions' => [
                [
                    'id' => 1,
                    'type' => 'Adaugare punct nou',
                    'point_type' => 'Punct colectare selectivă deșeuri (Container stradal)',
                    'location' => 'Strada Mihai Eminescu, nr. 1, București',
                    'date' => '2021-09-01 12:00:00',
                ],
            ],
        ]);
    }

    public function settings(): Response
    {
        return Inertia::render('Account/Settings', []);
    }

    public function profile(UpdateProfileRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return redirect()->back()->with('success', __('auth.profile_updated'));
    }

    public function password(ChangePasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->validated('password'),
        ]);

        return redirect()->back()->with('success', __('auth.password_updated'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $data = $request->validated();
        $user = auth()->user();
        $user->update($data);

        return redirect()->route('profile.edit')->with('success', __('auth.profile_updated'));
    }
}
