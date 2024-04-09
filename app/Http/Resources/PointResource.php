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
            'service' => $this->service_type->label(),
            'type' => $this->point_type_enum->label(),
            'source' => $this->source->label(),
            'name' => $this->name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'city' => $this->city->name,
            'country' => $this->county->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'notes' => $this->notes,
            'observations' => $this->observations,
            'schedule' => $this->schedule,
            'status' => $this->status->label(),
            'materials' => MaterialResource::collection($this->whenLoaded('materials')),

        ];
    }
}
