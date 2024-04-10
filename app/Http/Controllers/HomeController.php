<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Point\ServiceType;
use App\Http\Requests\MapRequest;
use App\Http\Resources\PointResource;
use App\Models\Material;
use App\Models\Point;
use Illuminate\Http\Request;
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
        $searchResult = [];
        if (! empty($request->search)) {
            $points = Point::query()->whereAny(
                [
                    'name',
                    'address',
                    'phone',
                    'email',
                    'website',
                    'notes',
                ],
                'LIKE',
                '%' . $request->search . '%'
            )->get();
            $nominatimResults = collect($this->getNominatimResults($request->search))->transform(function ($item) {
                return [
                    'name' => $item['display_name'],
                    'lat' => $item['lat'],
                    'lon' => $item['lon'],
                ];
            });
            $materials = Material::query()->where('name', 'LIKE', '%' . $request->search . '%')
                ->whereHas('points')
                ->get();
            $searchResult = [
                'points' => $points,
                'nominatim' => $nominatimResults,
                'materials' => $materials,
            ];
        }

        return Inertia::render('Home', [
            'service_types' => ServiceType::options(),
            'search_results' => $searchResult,
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

    /**
     * @param  MapRequest                                             $request
     * @return array|\SimpleXMLElement
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \NominatimLaravel\Exceptions\InvalidParameterException
     * @throws \NominatimLaravel\Exceptions\NominatimException
     */
    private function getNominatimResults(string $search): array|\SimpleXMLElement
    {
        $url = 'https://nominatim.openstreetmap.org';
        $nominatim = new Nominatim($url);
        $nominatimSearch = $nominatim->newSearch()
            ->polygon('geojson')    //or 'kml', 'svg' and 'text'
            ->query($search);

        return $nominatim->find($nominatimSearch);
    }
}
