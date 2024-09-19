<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Point;
use App\Models\Problem\Problem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContributionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Point */
        $point = match ($this->model_type) {
            (new Point)->getMorphClass() => $this->model,
            (new Problem)->getMorphClass() => $this->model->point,
        };

        return [
            'id' => $point->id,
            'point_type' => $point->pointType->name,
            'address' => $point->address,
            'created_at' => $this->created_at->isoFormat('Do MMM YYYY, HH:mm'),
            'url' => $point->url,
            'contribution_type' => $this->model_type,
            'problem_type' => match ($this->model_type) {
                (new Problem)->getMorphClass() => $this->model->type->name,
                (new Point)->getMorphClass() => null,
            },
        ];
    }
}
