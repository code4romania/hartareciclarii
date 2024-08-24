<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\DataTransferObjects\NominatimSuggestion;
use App\Models\Material;
use App\Models\Point;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return match (\get_class($this->resource)) {
            Point::class => $this->getPointArray(),
            Material::class => $this->getMaterialArray(),
            NominatimSuggestion::class => $this->getNominatimArray(),
        };
    }

    protected function getPointArray(): array
    {
        return [
            'name' => $this->name,
            'url' => route('point', $this),
            'type' => 'point',
            'icon' => $this->serviceType->slug,
        ];
    }

    protected function getMaterialArray(): array
    {
        return [
            'name' => $this->name,
            'url' => route('material', $this),
            'type' => 'material',
            'icon' => $this->icon,
        ];
    }

    protected function getNominatimArray(): array
    {
        return [
            'name' => $this->name,
            'type' => 'location',
            'bounds' => $this->bounds,
        ];
    }
}
