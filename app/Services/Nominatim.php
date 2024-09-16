<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransferObjects\Location;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
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

        $result = rescue(
            fn () => $this->nominatim->find($request),
            fn () => abort(Response::HTTP_SERVICE_UNAVAILABLE)
        );

        abort_if(data_get($result, 'error'), Response::HTTP_NOT_FOUND);

        return new Location($result);
    }

    public function maxBounds(): array
    {
        return Cache::remember('max-map-bounds', now()->addDay(), function () {
            $request = $this->nominatim->newSearch()
                ->country('ro')
                ->format('jsonv2');

            $bounds = collect(rescue(fn () => $this->nominatim->find($request)))
                ->filter(fn (array $result) => $result['place_rank'] === 4)
                ->take(1)
                ->pluck('boundingbox')
                ->flatten()
                ->map('floatval');

            if ($bounds->count() !== 4) {
                return null;
            }

            return [
                [$bounds[0], $bounds[2]],
                [$bounds[1], $bounds[3]],
            ];
        });
    }
}
