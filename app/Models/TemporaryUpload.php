<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TemporaryUpload extends Model implements HasMedia
{
    use InteractsWithMedia;
    use Prunable;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')
            ->registerMediaConversions(function (Media $media) {
                $this
                    ->addMediaConversion('thumb')
                    ->fit(Fit::Crop, 96, 96)
                    ->keepOriginalImageFormat()
                    ->optimize();
            });
    }
}
