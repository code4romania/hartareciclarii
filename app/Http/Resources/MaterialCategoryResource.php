<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'key' => $this->id,
            'label' => $this->name,
            'children' => MaterialResource::collection($this->materials),
        ];
    }
}