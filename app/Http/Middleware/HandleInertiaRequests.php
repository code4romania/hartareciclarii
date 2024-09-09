<?php

declare(strict_types=1);

namespace App\Http\Middleware;

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
                'auth' => $request->user(),
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
            'appName' => config('app.name'),
        ];
    }
}
