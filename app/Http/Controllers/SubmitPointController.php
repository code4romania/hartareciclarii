<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Concerns\CanAssignContributor;
use App\Concerns\CanAttachMedia;
use App\Http\Requests\SubmitPointRequest;
use App\Models\Point;
use Illuminate\Http\RedirectResponse;
use MatanYadaev\EloquentSpatial\Objects\Point as SpatialPoint;

class SubmitPointController extends Controller
{
    use CanAttachMedia;
    use CanAssignContributor;

    public function __invoke(SubmitPointRequest $request): RedirectResponse
    {
        $attributes = $request->validated();

        $attributes['location'] = new SpatialPoint(
            (float) $attributes['location']['lat'],
            (float) $attributes['location']['lng']
        );

        $point = Point::create($attributes);

        $this->assignContributor($point);

        $this->attachMaterials($point, data_get($attributes, 'materials'));

        $this->attachMedia($point, data_get($attributes, 'images'));

        return redirect()->to($point->url);
    }

    protected function attachMaterials(Point $point, ?array $materials): void
    {
        if (blank($materials)) {
            return;
        }

        $point->materials()->attach($materials);
    }
}
