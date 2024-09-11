<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->getFilamentAvatarUrl(),
            'created_at' => $this->created_at->isoFormat('Do MMMM YYYY'),
            'is_unverified' => ! $this->hasVerifiedEmail(),
            'show_verified_message' => $request->boolean('verified'),
        ];
    }
}
