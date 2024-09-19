<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SubmitImageRequest;
use App\Http\Resources\TemporaryUploadResource;
use App\Models\Media;
use App\Models\TemporaryUpload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\UploadedFile;

class MediaController extends Controller
{
    public function upload(SubmitImageRequest $request): ResourceCollection
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

    public function delete(Request $request, Media $media): JsonResponse
    {
        abort_unless($media->isTemporaryUpload(), 404);

        $media->delete();

        return response()->json();
    }
}
