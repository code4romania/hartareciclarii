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
        $issues = $this->status->is(Status::WITH_PROBLEMS)
         /*
          * @todo: Implement the following method:
          *
          * $this->issues()
          * ->pending()
          * ->get()
          */
            ? collect()
            : collect();

        return [
            'id' => $this->id,
            'name' => $this->pointType->name,
            'status' => [
                'color' => $this->status->getColor(),
                'label' => $this->status->getLabel(),
                'icon' => $this->status->getIcon(),
                'issues_count' => $issues->count(),
                'issues' => $issues,
            ],
            'latlng' => [$this->location->latitude, $this->location->longitude],
            'subheading' => $this->pointType->name . ' administrat de ' . $this->administered_by,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->website,
            'observations' => $this->observations,
            'schedule' => $this->schedule,
            'materials' => $this->getMaterialsByCategory(),
            'service' => $this->serviceType->slug,
            'info' => collect([
                'offers_money' => $this->offers_money,
                'offers_vouchers' => $this->offers_vouchers,
                'offers_transport' => $this->offers_transport,
                'free_of_charge' => $this->free_of_charge,
            ])->reject(fn (bool | null $value) => \is_null($value)),

        ];
    }

    protected function getMaterialsByCategory(): Collection
    {
        $materials = $this->materials()
            ->with('categories')
            ->get(['id', 'name']);

        $categories = $materials
            ->pluck('categories')
            ->flatten()
            ->unique('id');

        return $categories
            ->map(fn (MaterialCategory $category) => [
                'name' => $category->name,
                'icon' => $category->getFirstMediaUrl() ?: null,
                'materials' => $materials
                    ->where(fn (Material $material) => $material->categories->contains($category))
                    ->map(fn (Material $material) => [
                        'id' => $material->id,
                        'name' => $material->name,
                    ])
                    ->values(),
            ]);
    }
}
