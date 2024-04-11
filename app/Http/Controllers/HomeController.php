<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Point\ServiceType;
use App\Http\Requests\MapRequest;
use App\Http\Resources\PointResource;
use App\Models\Material;
use App\Models\Point;
use Inertia\Inertia;
use Inertia\Response;
use NominatimLaravel\Content\Nominatim;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(MapRequest $request): Response
    {
        return Inertia::render('Home', [
            'service_types' => ServiceType::options(),
            'search_results' => Inertia::lazy(fn () => $this->getSearchResults($request->search)),
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

    private function getNominatimResults(string $search): array
    {
        $url = 'https://nominatim.openstreetmap.org';
        $nominatim = new Nominatim($url);
        $nominatimSearch = $nominatim->newSearch()
            ->polygon('geojson')    //or 'kml', 'svg' and 'text'
            ->query($search);

        return $nominatim->find($nominatimSearch);
    }

    private function getSearchResults($search)
    {
        if (empty($search)) {
            return [];
        }

        $points = Point::query()
            ->whereAny(['name', 'address', 'phone', 'email', 'website', 'notes'], 'LIKE', '%' . $search . '%')
            ->get();

        $nominatimResults = collect($this->getNominatimResults($search))
            ->transform(fn ($item) => [
                'name' => $item['display_name'],
                'lat' => $item['lat'],
                'lng' => $item['lon'],
            ]);

        $materials = Material::query()->where('name', 'LIKE', '%' . $search . '%')
            ->whereHas('points')
            ->get();

        return [
            'points' => $points,
            'nominatim' => $nominatimResults,
            'materials' => $materials,
        ];
    }
}
