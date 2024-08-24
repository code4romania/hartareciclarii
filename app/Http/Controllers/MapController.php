<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DataTransferObjects\MapCoordinates;
use App\DataTransferObjects\NominatimSuggestion;
use App\Http\Requests\MapSearchRequest;
use App\Http\Resources\PointDetailsResource;
use App\Http\Resources\PointResource;
use App\Http\Resources\SearchResultResource;
use App\Http\Resources\ServiceTypeResource;
use App\Models\Material;
use App\Models\Point;
use App\Models\ServiceType;
use App\Services\Nominatim;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class MapController extends Controller
{
    public function index(MapCoordinates $coordinates): Response
    {
        return $this->render($coordinates);
    }

    public function point(Point $point, MapCoordinates $coordinates): Response
    {
        return $this->render($coordinates, [
            'type' => 'point',
            'point' => PointDetailsResource::make($point),
        ]);
    }

    public function material(Material $material, MapCoordinates $coordinates): Response
    {
        return $this->render($coordinates, [
            'type' => 'material',
            'material' => $material,
        ]);
    }

    public function suggest(Request $request): JsonResource
    {
        $attributes = $request->validate([
            'query' => ['required', 'string', 'min:3'],
        ]);

        $results = collect();

        $results->push(
            ...Point::search($attributes['query'])
                ->query(
                    fn (Builder $query) => $query
                        ->with('county')
                        ->with('city')
                        ->with('serviceType:id,slug')
                        // ->orderByDistance('location', $request->center)
                        ->limit(5)
                )
                ->get()
        );

        $results->push(
            ...Material::search($attributes['query'])
                ->query(
                    fn (Builder $query) => $query
                        ->with('categories')
                        ->whereHas('points'/* , fn (Builder $query) => $query->orderByDistance('location', $request->center) */)
                        ->limit(5)
                )
                ->get()
        );

        $results->push(
            ...Nominatim::search($attributes['query'], 5)
                ->map(
                    fn (array $suggestion) => new NominatimSuggestion($suggestion['display_name'], $suggestion['boundingbox'])
                )
        );

        return SearchResultResource::collection($results);
    }

    protected function render(MapCoordinates $coordinates, array $props = []): Response
    {
        $serviceTypes = ServiceType::query()
            ->with('pointTypes')
            ->get();

        return Inertia::render('Home', [
            'has_bounds' => $coordinates->hasBounds(),
            'center' => [
                $coordinates->getCenter()->latitude,
                $coordinates->getCenter()->longitude,
            ],
            'zoom' => $coordinates->getZoom(),

            'service_types' => ServiceTypeResource::collection($serviceTypes),
            'icons' => fn () => [
                'markercluster' => Vite::asset('resources/images/map/markercluster.svg'),
                ...$serviceTypes
                    ->mapWithKeys(fn (ServiceType $serviceType) => [
                        $serviceType->slug => [
                            'sm' => Vite::asset("resources/images/map/{$serviceType->slug}-sm.svg"),
                            'lg' => Vite::asset("resources/images/map/{$serviceType->slug}-lg.svg"),
                        ],
                    ]),
            ],

            'points' => function () use ($coordinates) {
                if (! $coordinates->hasBounds()) {
                    return [];
                }

                return PointResource::collection(
                    Point::query()
                        ->with('serviceType:id,slug')
                        ->whereWithin('location', $coordinates->getBounds())
                        ->orderByDistance('location', $coordinates->getCenter())
                        ->get()
                );
            },
            ...$props,
        ]);
    }

    public function search(MapSearchRequest $request): JsonResource
    {
        $attributes = $request->validated();

        $query = Str::of($attributes['query'])
            ->stripTags();

        $results = collect();

        $results->push(
            ...Point::search($query)
                ->query(
                    fn (Builder $query) => $query
                        ->with('county')
                        ->with('city')
                        ->with('serviceType:id,slug')
                        ->orderByDistance('location', $request->center)
                        ->limit(10)
                )
                ->get(),
            ...Material::search($query)
                ->query(
                    fn (Builder $query) => $query
                        ->with('categories')
                        ->whereHas('points', fn (Builder $query) => $query->orderByDistance('location', $request->center))
                        ->limit(10)
                )
                ->get(),
        );

        return SearchResultResource::collection($results);

        // return response()->json(
        //     $results

        //     [
        //     'points' => PointResource::collection(
        //         Point::search($query)
        //             ->query(
        //                 fn (Builder $query) => $query
        //                     ->with('serviceType:id,slug')
        //                     ->orderByDistance('location', $request->center)
        //                     ->limit(10)
        //             )
        //             ->get()
        //     ),
        // ]);
    }
}
