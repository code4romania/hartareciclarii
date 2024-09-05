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
            Point::class => $this->getPointArray(),
            Material::class => $this->getMaterialArray(),
            Location::class => $this->getLocationArray(),
        };
    }

    protected function getPointArray(): array
    {
        return [
            'name' => $this->business_name ?? $this->pointType->name,
            'description' => $this->address,
            'url' => route('front.map.point', $this),
            'type' => 'point',
            'icon' => $this->serviceType->slug,
        ];
    }

    protected function getMaterialArray(): array
    {
        return [
            'name' => $this->name,
            'url' => route('front.map.material', $this),
            'type' => 'material',
            'icon' => $this->icon,
        ];
    }

    protected function getLocationArray(): array
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
