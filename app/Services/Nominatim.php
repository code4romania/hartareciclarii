<?php

declare(strict_types=1);

namespace App\Services;

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
            ->take($this->limit);
    }
    }
}
