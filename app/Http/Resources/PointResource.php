<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PointResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'latitude' => $this->location->latitude,
            'longitude' => $this->location->longitude,
            'latlng' => [$this->location->latitude, $this->location->longitude],
            'name' => $this->name,
        ];
    }
}
