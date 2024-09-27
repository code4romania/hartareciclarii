<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DataTransferObjects\MapCoordinates;
use App\Enums\Point\Status;
use App\Http\Resources\MaterialCategoryResource;
use App\Http\Resources\MaterialResource;
use App\Http\Resources\PointDetailsResource;
use App\Http\Resources\PointResource;
use App\Http\Resources\ProblemTypeResource;
use App\Http\Resources\ReverseResource;
use App\Http\Resources\SearchResultResource;
use App\Http\Resources\ServiceTypeResource;
use App\Http\Resources\SuggestionResource;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\Point;
use App\Models\Problem\ProblemType;
use App\Models\Scopes\FilteredPointsScope;
use App\Models\ServiceType;
use App\Services\Nominatim;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Vite;
use Inertia\Inertia;
use Inertia\LazyProp;
use Inertia\Response;
use Laravel\Scout\Builder as ScoutBuilder;

class MapController extends Controller
{
    public function index(Request $request, MapCoordinates $coordinates): Response
    {
        if ($request->filters()->isEmpty()) {
            return $this->render($coordinates);
        }

        return $this->render($coordinates, [
            'context' => 'filter',
            'point' => $this->lazyFetchPoint($request),
        ]);
    }

    public function point(Point $point, MapCoordinates $coordinates): Response
    {
        return $this->render($coordinates, [
            'context' => 'point',
            'report' => false,
            'point' => PointDetailsResource::make($point),
        ]);
    }

    public function report(Point $point, MapCoordinates $coordinates): Response
    {
        return $this->render($coordinates, [
            'context' => 'point',
            'report' => true,
            'point' => PointDetailsResource::make($point),
            'problem_types' => ProblemTypeResource::collection(
                ProblemType::query()
                    ->whereNull('parent_id')
                    ->whereValidForServiceTypeId($point->service_type_id)
                    ->with('children')
                    ->get()
            ),
        ]);
    }

    public function suggest(Request $request, MapCoordinates $coordinates): JsonResource
    {
        $attributes = $request->validate([
            'query' => ['required', 'string'],
            'type' => ['nullable', 'string', 'in:point,material,location'],
        ]);

        $type = data_get($attributes, 'type');

        $results = collect();

        if (blank($type) || $type === 'point') {
            $results->push(
                ...Point::search($attributes['query'])
                    ->take(5)
                    ->query(
                        fn (Builder $query) => $query
                            ->with('serviceType:id,slug')
                            ->with('pointType')
                            ->orderByDistance('location', $coordinates->getCenter())
                    )
                    ->get()
            );
        }

        if (blank($type) || $type === 'material') {
            $results->push(
                ...Material::search($attributes['query'])
                    ->take(5)
                    ->query(
                        fn (Builder $query) => $query
                            ->with('categories.media')
                            ->whereHas('points', fn (Builder $query) => $query->orderByDistance('location', $coordinates->getCenter()))
                    )
                    ->get()
            );
        }

        if (blank($type) || $type === 'location') {
            $results->push(
                ...Nominatim::make()
                    ->limit(5)
                    ->viewBox($coordinates->getBoundsBBox())
                    ->search($attributes['query'])
            );
        }

        return SuggestionResource::collection($results);
    }

    public function reverse(Request $request, MapCoordinates $coordinates): JsonResource
    {
        return ReverseResource::make(
            Nominatim::make()
                ->reverse($coordinates->latitude, $coordinates->longitude)
        );
    }

    public function search(Request $request, MapCoordinates $coordinates): Response
    {
        $attributes = $request->validate([
            'query' => ['required', 'string'],
            'material' => ['nullable', 'integer'],
        ]);

        return $this->render($coordinates, [
            'context' => 'search',
            'query' => $attributes['query'],
            'points' => function () use ($attributes, $coordinates) {
                if (! $coordinates->hasBounds()) {
                    return [];
                }

                $material = data_get($attributes, 'material');

                return SearchResultResource::collection(
                    Point::search($attributes['query'])
                        ->when($material, fn (ScoutBuilder $query) => $query->where('material_ids', $material))
                        ->take(100)
                        ->query(
                            fn (Builder $query) => $query
                                ->with('serviceType:id,slug')
                                ->with('pointType:id,name')
                                ->whereMatchesCoordinates($coordinates)
                        )
                        ->get()
                );
            },
            'point' => $this->lazyFetchPoint($request),
        ]);
    }

    protected function render(MapCoordinates $coordinates, array $props = []): Response
    {
        $serviceTypes = ServiceType::query()
            ->with('pointTypes')
            ->get();

        return Inertia::render('Map', [
            'mapOptions' => [
                'bounds' => $coordinates->getBoundsBBox(),
                'center' => [
                    'lat' => $coordinates->getCenter()->latitude,
                    'lng' => $coordinates->getCenter()->longitude,
                ],
                'zoom' => $coordinates->getZoom(),
            ],

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

            'materials' => fn () => [
                'categories' => MaterialCategoryResource::collection(
                    MaterialCategory::query()
                        ->with('media')
                        ->get()
                ),
                'items' => MaterialResource::collection(
                    Material::query()
                        ->with('categories')
                        ->get()
                ),
            ],

            'statuses' => collect(Status::options())
                ->reject(fn ($value, $key) => Status::WITH_PROBLEMS->is($key))
                ->map(fn ($value, $key) => [
                    'value' => $key,
                    'label' => $value,
                ])
                ->values(),

            'filter' => request()->filters(),

            'points' => function () use ($coordinates) {
                if (! $coordinates->hasBounds()) {
                    return [];
                }

                return PointResource::collection(
                    Point::query()
                        ->with('serviceType:id,slug')
                        ->whereMatchesCoordinates($coordinates)
                        ->withGlobalScope('filtered', new FilteredPointsScope(request()->filters()))
                        ->take(2000)
                        ->get(['id', 'location', 'service_type_id'])
                );
            },

            ...$props,
        ]);
    }

    protected function lazyFetchPoint(Request $request): LazyProp
    {
        return Inertia::lazy(function () use ($request) {
            if (! $request->hasHeader('Map-Point')) {
                return null;
            }

            return PointDetailsResource::make(
                Point::findOrFail($request->header('Map-Point'))
            );
        });
    }
}
