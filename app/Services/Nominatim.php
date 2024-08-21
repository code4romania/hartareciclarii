<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use NominatimLaravel\Content\Nominatim as NominatimClient;

class Nominatim
{
    public static function search(string $query): Collection
    {
        $nominatim = new NominatimClient(config('services.nominatim.url'));

        $request = $nominatim->newSearch()
            ->polygon('geojson')
            ->query($query);

        return collect($nominatim->find($request))
            ->map(fn (array $item) => [
                'name' => $item['display_name'],
                'lat' => $item['lat'],
                'lng' => $item['lon'],
            ]);
    }
}
