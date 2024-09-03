<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceTypeResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'can' => [
                'have_business_name' => $this->can_have_business_name,
                'offer_money' => $this->can_offer_money,
                'offer_vouchers' => $this->can_offer_vouchers,
                'offer_transportation' => $this->can_offer_transport,
                'request_payment' => $this->can_request_payment,
                'collect_materials' => $this->can_collect_materials,
            ],
            'point_types' => PointTypeResource::collection($this->pointTypes),
        ];
    }
}
