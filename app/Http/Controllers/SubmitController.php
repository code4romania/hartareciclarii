<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Point\Status;
use App\Http\Requests\SubmitImageRequest;
use App\Http\Requests\SubmitPointRequest;
use App\Http\Resources\PointImageUploadResource;
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

        return PointImageUploadResource::collection($temporaryUpload->media);
    }

    public function deleteImage(Request $request, Media $media): JsonResponse
    {
        abort_unless($media->isTemporaryUpload(), 404);

        $media->delete();

        return response()->json();
    }
}
