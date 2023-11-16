<?php

namespace App\Http\Resources;

use App\Models\MapPointField;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MapPointTypeResource extends JsonResource
{
	public $collects = MapPointField::class;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		return [
			'id' => $this->id,
			'service_id' => $this->service_id,
			'type_name' => $this->type_name,
			'display_name' => $this->display_name,
			'icon' => $this->icon,
		];
    }
}
