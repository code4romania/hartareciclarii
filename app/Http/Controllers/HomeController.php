<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Point\ServiceType;
use App\Http\Requests\MapRequest;
use App\Http\Resources\PointResource;
use App\Models\Point;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(MapRequest $request): Response
    {
        return Inertia::render('Home', [
            'service_types' => ServiceType::options(),
            'point_types' => collect(ServiceType::cases())
                ->mapWithKeys(fn (ServiceType $serviceType) => [
                    $serviceType->value => $serviceType->pointTypes()::options(),
                ]),

            'points' => Inertia::lazy(
                fn () => PointResource::collection(
                    Point::query()
                        ->whereWithin('location', $request->bounds)
                        ->get()
                )
            ),
        ]);
    }
}
