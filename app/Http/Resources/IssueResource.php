<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IssueResource extends JsonResource
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
            'point_id' => $this->point_id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'reporter_id' => $this->reporter_id,
            'status' => $this->status,
            'reported_point_issue_type_id' => $this->reported_point_issue_type_id,
            'reported_point_issue_type' => $this->type,
            'material_issue' => $this->material_issue,
            'material_issue_missing' => $this->material_issue_missing,
            'material_issue_extra' => $this->material_issue_extra,
            'collection_decline_reason' => $this->collection_decline_reason,
            'description' => $this->description,

        ];
    }
}
