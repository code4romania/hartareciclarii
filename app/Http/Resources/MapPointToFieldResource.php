<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MapPointToFieldResource extends JsonResource
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
            'field_type_id' => $this->field_type_id,
            'recycling_point_id' => $this->recycling_point_id,
            'value' => $this->value,
            'upvotes' => $this->upvotes,
            'downvotes' => $this->downvotes,
            'field' => new MapPointFieldResource($this->field),
        ];
    }
}
