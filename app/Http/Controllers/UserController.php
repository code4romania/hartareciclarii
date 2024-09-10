<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Auth\UpdatePasswordRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
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

    public function edit()
    {
        $profile = auth()->user();

        return Inertia::render('Edit', ['profile' => $profile]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $data = $request->validated();
        $user = auth()->user();
        $user->update($data);

        return redirect()->route('profile.edit')->with('success', __('auth.profile_updated'));
    }

    public function changePassword(UpdatePasswordRequest $request)
    {
        $data = $request->validated();
        $user = auth()->user();
        if (! \Hash::check($data['current_password'], $user->password)) {
            return redirect()->back()->withErrors(['current_password' => __('auth.password_does_not_match')]);
        }
        $user->update(['password' => \Hash::make($data['password'])]);

        return redirect()->route('profile.edit')->with('success', __('auth.password_updated'));
    }
}
