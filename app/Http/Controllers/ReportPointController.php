<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Concerns\CanAssignContributor;
use App\Concerns\CanAttachMedia;
use App\Http\Requests\ReportPointRequest;
use App\Models\Point;
use App\Models\Problem\Problem;
use Illuminate\Http\RedirectResponse;
use MatanYadaev\EloquentSpatial\Objects\Point as SpatialPoint;

class ReportPointController extends Controller
{
    use CanAttachMedia;
    use CanAssignContributor;

    public function __invoke(ReportPointRequest $request, Point $point): RedirectResponse
    {
        $attributes = $request->validated();

        $attributes['location'] = $this->getLocation(data_get($attributes, 'location'));

        /** @var Problem */
        $problem = $point->problems()->create($attributes);

        $this->assignContributor($problem);

        $this->attachMaterials($problem, data_get($attributes, 'materials_add'), data_get($attributes, 'materials_remove'));

        $this->attachSubTypes($problem, data_get($attributes, 'sub_types'));

        $this->attachMedia($problem, data_get($attributes, 'images'));

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
}
