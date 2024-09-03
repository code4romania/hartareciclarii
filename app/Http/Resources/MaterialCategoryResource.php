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
            'key' => "cat-{$this->id}",
            'label' => $this->name,
            'type' => 'category',
            'icon' => $this->getFirstMediaUrl() ?: null,
            'children' => MaterialResource::collection($this->materials),
        ];
    }
}
