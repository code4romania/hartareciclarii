<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class IssueTypeResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
	
    public function toArray(Request $request): array
    {
        return [
			'id' => $this->id,
			'accept_images' => $this->accept_images,
			'title' => $this->title,
			'steps' => $this->steps,
			'items' => IssueTypeItemResource::collection($this->items)
		];
    }
}
