<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Point\Status;
use App\Http\Requests\ReportPointRequest;
use App\Http\Requests\SubmitImageRequest;
use App\Http\Requests\SubmitPointRequest;
use App\Http\Resources\TemporaryUploadResource;
use App\Models\Media;
use App\Models\Point;
use App\Models\TemporaryUpload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\UploadedFile;
use MatanYadaev\EloquentSpatial\Objects\Point as SpatialPoint;

class SubmitController extends Controller
{
    public function point(SubmitPointRequest $request): RedirectResponse
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

    public function report(ReportPointRequest $request, Point $point): RedirectResponse
    {
        $attributes = $request->validated();

        if (auth()->check()) {
            $attributes['reported_by'] = auth()->id();
        }

        tap(
            data_get($attributes, 'location'),
            function (?array $location) use ($attributes) {
                if (\is_null($location)) {
                    return;
                }

                $attributes['location'] = new SpatialPoint(
                    (float) $location['lat'],
                    (float) $location['lng']
                );
            }
        );

        $problem = $point->problems()->create($attributes);

        tap(
            data_get($attributes, 'materials'),
            function (?array $materials) use ($problem) {
                if (filled($materials)) {
                    $problem->materials()->attach($materials);
                }
            }
        );

        tap(
            data_get($attributes, 'images'),
            function (?array $images) use ($problem) {
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
        );

        return redirect()->back();
    }

    public function image(SubmitImageRequest $request): ResourceCollection
    {
        $attributes = $request->validated();

        $temporaryUpload = TemporaryUpload::create();

        collect($attributes['images'])
            ->each(
                fn (UploadedFile $image) => $temporaryUpload
                    ->addMedia($image)
                    ->toMediaCollection()
            );

        return TemporaryUploadResource::collection($temporaryUpload->media);
    }

    public function deleteImage(Request $request, Media $media): JsonResponse
    {
        abort_unless($media->isTemporaryUpload(), 404);

        $media->delete();

        return response()->json();
    }
}
