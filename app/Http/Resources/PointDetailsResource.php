<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\Point\Status;
use App\Models\Material;
use App\Models\MaterialCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class PointDetailsResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        $problems = $this->status->is(Status::WITH_PROBLEMS)
            ? $this->problems()
                ->with('type')
                ->whereOpen()
                ->get(['id', 'type_id'])
            : collect();

        $materials = $this->serviceType->can_collect_materials
            ? $this->materials()
                ->with('categories.media')
                ->get(['id', 'name', 'url'])
            : collect();

        return [
            'id' => $this->id,
            'name' => $this->pointType->name,
            'is_sgr' => (bool)$this->pointType->is_sgr,
            'status' => [
                'color' => $this->status->getColor(),
                'label' => $this->status->is(Status::WITH_PROBLEMS)
                    ? trans_choice('enums.point_status.problems_count', $problems->count())
                    : $this->status->getLabel(),
                'icon' => $this->status->getIcon(),
                'problems' => $problems
                    ->pluck('type.name')
                    ->unique()
                    ->values(),
            ],
            'latlng' => [$this->location->latitude, $this->location->longitude],
            'subheading' => __('point.service_administered', [
                'service' => $this->serviceType->name,
                'administrator' => $this->administered_by,
            ]),
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'observations' => $this->observations,
            'schedule' => $this->schedule,
            'materials' => $this->getMaterialsByCategory($materials),
            'material_ids' => $materials->pluck('id'),
            'service' => $this->serviceType->slug,
            'info' => collect([
                'offers_money' => $this->offers_money,
                'offers_vouchers' => $this->offers_vouchers,
                'offers_transport' => $this->offers_transport,
                'free_of_charge' => $this->free_of_charge,
            ])->reject(fn (bool | null $value) => \is_null($value)),
        ];
    }

    protected function getMaterialsByCategory(Collection $materials): Collection
    {
        return $materials
            ->pluck('categories')
            ->flatten()
            ->unique('id')
            ->map(fn (MaterialCategory $category) => [
                'name' => $category->name,
                'icon' => $category->getFirstMediaUrl() ?: null,
                'materials' => $materials
                    ->where(fn (Material $material) => $material->categories->contains($category))
                    ->map(fn (Material $material) => [
                        'id' => $material->id,
                        'name' => $material->name,
                        'url' => $material->url,
                    ])
                    ->values(),
            ])
            ->values();
    }
}
