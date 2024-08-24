<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

readonly class NominatimSuggestion
{
    public function __construct(
        public string $name,
        public array $bounds
    ) {
    }
}
