<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;

class SearchResultResource extends PointResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'id' => $this->id,
            'name' => $this->pointType->name,
            'service' => $this->serviceType->slug,
            'latlng' => [$this->location->latitude, $this->location->longitude],
            'subheading' => $this->pointType->name . ' administrat de ' . $this->administered_by,
            'address' => $this->address,
        ]);
    }
}
