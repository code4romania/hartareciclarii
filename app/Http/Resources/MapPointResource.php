<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MapPointResource extends JsonResource
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
			'point_type_id' => $this->point_type_id,
			'lat' => $this->lat,
			'lon' => $this->lon,
			'group_id' => $this->group_id,
			'service_id' => $this->service_id,
			'created_by' => $this->created_by,
			'type' => new MapPointTypeResource($this->type),
			'service' => new MapPointServiceResource($this->service),
			'fields' => MapPointToFieldResource::collection($this->fields),
			'materials' => RecycleMaterialResource::collection($this->materials),
		];
    }
}
