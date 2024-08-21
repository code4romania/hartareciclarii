<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\MapRequest;
use App\Http\Resources\PointDetailsResource;
use App\Http\Resources\PointResource;
use App\Models\Material;
use App\Models\Point;
use App\Models\ServiceType;
use App\Services\Nominatim;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Vite;
use Inertia\Inertia;
use Inertia\Response;
use MatanYadaev\EloquentSpatial\Objects\Point as PointObject;

class HomeController extends Controller
{
    public function index(MapRequest $request): Response
    {
        return $this->render($request);
    }

    public function point(MapRequest $request, Point $point): Response
    {
        return $this->render($request, [
            'type' => 'point',
            'point' => PointDetailsResource::make($point),
        ]);
    }

    public function material(MapRequest $request, Material $material): Response
    {
        return $this->render($request, [
            'type' => 'material',
            'material' => $material,
        ]);
    }

    protected function render(MapRequest $request, array $props = []): Response
    {
        $serviceTypes = ServiceType::all(['id', 'name', 'slug']);

        return Inertia::render('Home', [
            'icons' => $this->getIcons($serviceTypes),
            'service_types' => $serviceTypes,
            'search_results' => Inertia::lazy(fn () => $this->getSearchResults($request->search, $request->center)),
            // 'point_types' => collect(ServiceType::cases())
            //     ->mapWithKeys(fn (ServiceType $serviceType) => [
            //         $serviceType->value => $serviceType->pointTypes()::options(),
            //     ]),

            'points' => function () use ($request) {
                if (blank($request->bounds) || blank($request->center)) {
                    return [];
                }

                return PointResource::collection(
                    Point::query()
                        ->with('serviceType:id,slug')
                        ->whereWithin('location', $request->bounds)
                        ->orderByDistance('location', $request->center)
                        ->get()
                );
            },
            ...$props,
        ]);
    }

    private function getSearchResults(?string $query, PointObject $center)
    {
        if (blank($query)) {
            return [];
        }

        return [
            'points' => Point::query()
                ->whereAny(['name', 'address', 'phone', 'email', 'website', 'notes'], 'LIKE', '%' . $query . '%')
                ->orderByDistance('location', $center)
                ->limit(10)
                ->get(),

            'materials' => Material::query()
                ->where('name', 'LIKE', '%' . $query . '%')
                ->whereHas('points', fn (Builder $query) => $query->orderByDistance('location', $center))
                ->limit(10)
                ->get(),

            'nominatim' => Nominatim::search($query),
        ];
    }

    private function getIcons(Collection $serviceTypes): array
    {
        return [
            'markercluster' => Vite::asset('resources/images/map/markercluster.svg'),
            ...$serviceTypes
                ->mapWithKeys(fn (ServiceType $serviceType) => [
                    $serviceType->slug => [
                        'sm' => Vite::asset("resources/images/map/{$serviceType->slug}-sm.svg"),
                        'lg' => Vite::asset("resources/images/map/{$serviceType->slug}-lg.svg"),
                    ],
                ]),
        ];
    }
}
