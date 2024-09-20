<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\ContributionResource;
use App\Models\Contribution;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function dashboard(): Response
    {
        $query = Contribution::query()
            ->where('user_id', auth()->id());

        return Inertia::render('Account/Dashboard', [
            'contributions_count' => $query->count(),

            'contributions' => ContributionResource::collection(
                $query
                    ->withPointData()
                    ->orderByDesc('created_at')
                    ->paginate()
            )->additional([
                'columns' => [
                    [
                        'key' => 'id',
                        'label' => __('contributions.column.id'),
                    ],
                    [
                        'key' => 'point_type',
                        'label' => __('contributions.column.point_type'),
                        'highlight' => true,
                    ],
                    [
                        'key' => 'address',
                        'label' => __('contributions.column.address'),
                    ],
                    [
                        'key' => 'contribution_type',
                        'label' => __('contributions.column.contribution_type'),
                    ],
                    [
                        'key' => 'created_at',
                        'label' => __('contributions.column.created_at'),
                    ],
                    [
                        'key' => 'actions',
                    ],
                ],
            ]),
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
