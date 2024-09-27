<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\DataTransferObjects\Location;
use App\Models\Material;
use App\Models\Point;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuggestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return match (\get_class($this->resource)) {
            Point::class => $this->getPointArray($request),
            Material::class => $this->getMaterialArray($request),
            Location::class => $this->getLocationArray($request),
        };
    }

    protected function getPointArray(Request $request): array
    {
        return [
            'name' => $this->business_name ?? $this->pointType->name,
            'description' => $this->address,
            'type' => 'point',
            'icon' => $this->serviceType->slug,
            'url' => $this->url,
        ];
    }

    protected function getMaterialArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'type' => 'material',
            'icon' => $this->icon,
            'url' => route('front.map.search', [
                'coordinates' => $request->coordinates,
                'query' => $this->name,
                'material' => $this->id,
            ]),
        ];
    }

    protected function getLocationArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'type' => 'location',
            'bounds' => $this->bounds,
            'center' => $this->center,
            'city' => $this->city,
            'county' => $this->county,
        ];
    }
}
