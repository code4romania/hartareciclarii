<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    public function share(Request $request): array
    {
        return array_merge(
            parent::share($request),
            $this->shareOnce($request),
            [
                'appName' => config('app.name'),
                'auth' => $this->getCurrentUser($request->user()),
                'flash' => fn () => $this->flash($request),
            ]
        );
    }

    protected function shareOnce(Request $request): array
    {
        if ($request->inertia()) {
            return [];
        }

        return [
            'recaptcha_site_key' => config('recaptcha.api_site_key'),
        ];
    }

    protected function getCurrentUser(?User $user): ?UserResource
    {
        return $user instanceof User
            ? UserResource::make($user)
            : null;
    }

    protected function flash(Request $request): ?array
    {
        if (! $request->hasSession()) {
            return null;
        }

        $type = match (true) {
            $request->session()->has('error') => 'error',
            $request->session()->has('success') => 'success',
            default => null,
        };

        if ($type === null) {
            return null;
        }

        return [
            'success' => $type === 'success',
            'message' => $request->session()->get($type),
        ];
    }
}
