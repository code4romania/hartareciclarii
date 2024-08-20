<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PointDetailsResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => [
                'color' => $this->status->getColor(),
                'label' => $this->status->getLabel(),
                'icon' => $this->status->getIcon(),
            ],
            'latlng' => [$this->location->latitude, $this->location->longitude],
            'subheading' => $this->pointType->name . ' administrat de ' . $this->administered_by,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'observations' => $this->observations,
            'schedule' => $this->schedule,
            'offers' => [
                'money' => $this->offers_money,
                'vouchers' => $this->offers_vouchers,
                'transport' => $this->offers_transport,
            ],
            'free_of_charge' => $this->free_of_charge,
        ];
    }
}
