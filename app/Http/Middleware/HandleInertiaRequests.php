<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    public function share(Request $request): array
    {
        return array_merge(
            parent::share($request),
            $this->shareOnce($request, fn (Request $request) => [
                'recaptcha_site_key' => config('recaptcha.api_site_key'),
            ]),
            [
                'user' => $request->user(),
                'everyrequest' => true,
            ]
        );
    }

    public function shareOnce(Request $request, Closure $callback): array
    {
        if ($request->inertia()) {
            return [];
        }

        return Arr::wrap($callback($request));
    }
}
