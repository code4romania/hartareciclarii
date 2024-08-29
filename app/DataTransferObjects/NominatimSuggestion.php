<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Support\Collection;

readonly class NominatimSuggestion
{
    public string $name;

    public array $bounds;

    public array $center;

    public function __construct(array $suggestion)
    {
        $this->name = $this->computeName(collect($suggestion['address']));
        $this->bounds = $suggestion['boundingbox'];

        $this->center = [
            $suggestion['lat'],
            $suggestion['lon'],
        ];
    }

    protected function computeName(Collection $address): string
    {
        $street = $address->get('road');

        if ($address->has('house_number')) {
            $street .= ' nr. ' . $address->get('house_number');
        }

        $parts = [
            $this->first(['emergency', 'historic', 'military', 'natural', 'landuse', 'place', 'railway', 'man_made', 'aerialway', 'boundary', 'amenity', 'aeroway', 'club', 'craft', 'leisure', 'office', 'mountain_pass', 'shop', 'tourism', 'bridge', 'tunnel', 'waterway'], $address),
            $street,
            $address->get('building'),
            $this->first(['city_district', 'district'], $address),
            $this->first(['city', 'town', 'village'], $address),
            $address->get('county'),
        ];

        return collect($parts)
            ->filter()
            ->join(', ');
    }

    protected function first(array $keys, Collection $collection): ?string
    {
        foreach ($keys as $key) {
            if ($collection->has($key)) {
                return $collection->get($key);
            }
        }

        return null;
    }
}
