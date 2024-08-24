<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use NominatimLaravel\Content\Nominatim as NominatimClient;

class Nominatim
{
    public static function search(string $query, int $limit = 5): Collection
    {
        $nominatim = new NominatimClient(config('services.nominatim.url'));

        $request = $nominatim->newSearch()
            ->language(app()->getLocale())
            ->addressDetails()
            ->countryCode('ro')
            ->format('jsonv2')
            ->query($query)
            ->limit($limit);

        return collect($nominatim->find($request));
    }
}
