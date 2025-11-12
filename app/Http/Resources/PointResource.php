<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PointResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service' => $this->serviceType->slug,
            'latlng' => [$this->location->latitude, $this->location->longitude],
            'is_sgr' => $this->pointType->is_sgr,
        ];
    }
}
