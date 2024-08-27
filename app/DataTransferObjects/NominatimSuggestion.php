<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

readonly class NominatimSuggestion
{
    public string $name;

    public array $bounds;

    public array $center;

    public function __construct(array $suggestion)
    {
        $this->name = $suggestion['display_name'];
        $this->bounds = $suggestion['boundingbox'];

        $this->center = [
            $suggestion['lat'],
            $suggestion['lon'],
        ];
    }
}
