<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransferObjects\Location;
use Illuminate\Support\Collection;
use NominatimLaravel\Content\Nominatim as NominatimClient;

class Nominatim
{
    private NominatimClient $nominatim;

    private int $limit = 5;

    private array $viewBox = [];

    public function __construct()
    {
        $this->nominatim = new NominatimClient(config('services.nominatim.url'));
    }

    public static function make(): static
    {
        return new static;
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function viewBox(?string $viewBox): static
    {
        if (filled($viewBox)) {
            $this->viewBox = explode(',', $viewBox);
        }

        return $this;
    }

    /**
     * @see https://nominatim.org/release-docs/latest/customize/Ranking/#search-rank For possible place_rank values.
     */
    public function search(string $query): Collection
    {
        $request = $this->nominatim->newSearch()
            ->language(app()->getLocale())
            ->addressDetails()
            ->countryCode('ro')
            ->format('jsonv2')
            ->query($query);

        if (filled($this->viewBox) && \count($this->viewBox) === 4) {
            $request->viewBox(...$this->viewBox);
        }

        return collect(rescue(fn () => $this->nominatim->find($request)))
            ->filter(fn (array $result) => $result['place_rank'] >= 13)
            ->take($this->limit)
            ->map(fn (array $result) => new Location($result));
    }

    public function reverse(float $latitude, float $longitude): Location
    {
        $request = $this->nominatim->newReverse()
            ->language(app()->getLocale())
            ->addressDetails()
            ->format('jsonv2')
            ->zoom(18)
            ->latlon($latitude, $longitude);

        $result = $this->nominatim->find($request);

        abort_if(data_get($result, 'error'), 404);

        return new Location($result);
    }

    public static function getPlaceById(int $placeId): Collection
    {
        $nominatim = new NominatimClient(config('services.nominatim.url'));

        $request = $nominatim->newDetails()
            ->language(app()->getLocale())
            ->addressDetails()
            ->placeId($placeId);


        return collect(
            rescue(fn () => $nominatim->find($request))
        );
    }
}
