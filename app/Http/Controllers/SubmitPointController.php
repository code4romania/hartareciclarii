<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Point\Status;
use App\Http\Requests\SubmitPointRequest;
use App\Models\Media;
use App\Models\Point;
use Illuminate\Http\RedirectResponse;
use MatanYadaev\EloquentSpatial\Objects\Point as SpatialPoint;

class SubmitPointController extends Controller
{
    public function __invoke(SubmitPointRequest $request): RedirectResponse
    {
        $attributes = $request->validated();

        $attributes['location'] = new SpatialPoint(
            (float) $attributes['location']['lat'],
            (float) $attributes['location']['lng']
        );

        $attributes['status'] = Status::NEEDS_VERIFICATION;

        $point = Point::create($attributes);

        $point->materials()->attach(data_get($attributes, 'materials', []));

        if (filled($attributes['images'])) {
            Media::query()
                ->whereIn('uuid', data_get($attributes, 'images'))
                ->update([
                    'model_id' => $point->getKey(),
                    'model_type' => $point->getMorphClass(),
                ]);
        }

        return redirect()->to($point->url);
    }
}
