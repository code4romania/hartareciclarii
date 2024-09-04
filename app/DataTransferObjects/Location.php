<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Illuminate\Support\Collection;

readonly class Location
{
    public string $name;

    public array $bounds;

    public array $center;

    public Collection $address;

    public ?string $county;

    public ?string $city;

    public function __construct(array $location)
    {
        $this->address = $this->collectAddress($location);

        $this->county = $this->firstFromAddress(['county'], 'București');
        $this->city = $this->firstFromAddress(['city_district', 'district', 'city', 'town', 'village']);

        $this->name = $this->computeName();
        $this->bounds = collect($location['boundingbox'])
            ->map([$this, 'formatCoordinate'])
            ->all();

        $this->center = [
            $this->formatCoordinate($location['lat']),
            $this->formatCoordinate($location['lon']),
        ];
    }

    protected function collectAddress(array $location): Collection
    {
        return collect(data_get($location, 'address'))
            ->reject(fn ($value, $key) => \in_array($key, [
                'ISO3166-2-lvl4', 'ISO3166-2-lvl8', 'postcode', 'country_code', 'country',
            ]));
    }

    protected function computeName(): string
    {
        $street = $this->address->get('road');

        if ($this->address->has('house_number')) {
            $street .= ' ' . $this->address->get('house_number');
        }

        $parts = [
            $this->firstFromAddress(['emergency', 'historic', 'military', 'natural', 'landuse', 'place', 'railway', 'man_made', 'aerialway', 'boundary', 'amenity', 'aeroway', 'club', 'craft', 'leisure', 'office', 'mountain_pass', 'shop', 'tourism', 'bridge', 'tunnel', 'waterway']),
            $street,
            $this->address->get('building'),
            $this->city,
            $this->county,
        ];

        return collect($parts)
            ->filter()
            ->join(', ');
    }

    protected function firstFromAddress(array $keys, ?string $default = null): ?string
    {
        foreach ($keys as $key) {
            if ($this->address->has($key)) {
                return $this->address->get($key);
            }
        }

        return $default;
    }

    public function formatCoordinate(string | float $coordinate): float
    {
        return (float) number_format((float) $coordinate, 6);
    }
}
