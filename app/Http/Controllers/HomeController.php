<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Point\ServiceType;
use App\Http\Requests\MapRequest;
use App\Http\Resources\PointResource;
use App\Models\Material;
use App\Models\Point;
use App\Services\Nominatim;
use Illuminate\Database\Eloquent\Builder;
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
            'point' => new PointResource($point),
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
        return Inertia::render('Home', [
            'service_types' => ServiceType::options(),
            'search_results' => Inertia::lazy(fn () => $this->getSearchResults($request->search, $request->center)),
            'point_types' => collect(ServiceType::cases())
                ->mapWithKeys(fn (ServiceType $serviceType) => [
                    $serviceType->value => $serviceType->pointTypes()::options(),
                ]),
            'points' => Inertia::lazy(
                fn () => PointResource::collection(
                    Point::query()
                        ->when(filled($request->bounds), function (Builder $query) use ($request) {
                            $query->whereWithin('location', $request->bounds)
                                ->orderByDistance('location', $request->center);
                        })
                        ->get()
                )
            ),
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
}
