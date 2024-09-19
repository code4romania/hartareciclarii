<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ReportPointRequest;
use App\Models\Media;
use App\Models\Point;
use App\Models\Problem\Problem;
use Illuminate\Http\RedirectResponse;
use MatanYadaev\EloquentSpatial\Objects\Point as SpatialPoint;

class ReportPointController extends Controller
{
    public function __invoke(ReportPointRequest $request, Point $point): RedirectResponse
    {
        $attributes = $request->validated();

        if (auth()->check()) {
            $attributes['reported_by'] = auth()->id();
        }

        $attributes['location'] = $this->getLocation(data_get($attributes, 'location'));

        $problem = $point->problems()->create($attributes);

        $this->attachMaterials($problem, data_get($attributes, 'materials_add'), data_get($attributes, 'materials_remove'));

        $this->attachSubTypes($problem, data_get($attributes, 'sub_types'));

        $this->attachImages($problem, data_get($attributes, 'images'));

        return redirect()->back();
    }

    protected function getLocation(?array $location): ?SpatialPoint
    {
        if (\is_null($location)) {
            return null;
        }

        return new SpatialPoint(
            (float) $location['lat'],
            (float) $location['lng']
        );
    }

    protected function attachMaterials(Problem $problem, ?array $materialsAdd, ?array $materialsRemove): void
    {
        $materialsAdd = collect($materialsAdd)
            ->mapWithKeys(fn ($material) => [$material => ['flag' => true]]);

        $materialsRemove = collect($materialsRemove)
            ->mapWithKeys(fn ($material) => [$material => ['flag' => false]]);

        $problem->materials()->sync($materialsAdd->union($materialsRemove));
    }

    protected function attachSubTypes(Problem $problem, ?array $subTypes): void
    {
        if (blank($subTypes)) {
            return;
        }

        $problem->subTypes()->attach($subTypes);
    }

    protected function attachImages(Problem $problem, ?array $images): void
    {
        if (blank($images)) {
            return;
        }

        Media::query()
            ->whereIn('uuid', $images)
            ->update([
                'model_id' => $problem->getKey(),
                'model_type' => $problem->getMorphClass(),
            ]);
    }
}
