<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SubmitPointRequest;
use App\Models\Point;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Http\RedirectResponse;
use MatanYadaev\EloquentSpatial\Objects\Point as SpatialPoint;

class SubmitPointController extends Controller
{
    public function __construct()
    {
        $this->middleware(HandlePrecognitiveRequests::class);
    }

    public function __invoke(SubmitPointRequest $request): RedirectResponse
    {
        $attributes = $request->validated();

        $attributes['location'] = new SpatialPoint(
            $attributes['location']['lat'],
            $attributes['location']['lng']
        );

        $point = Point::create($attributes);

        return redirect()->route('front.map.point', $point);
    }
}
