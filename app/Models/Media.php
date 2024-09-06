<?php

declare(strict_types=1);

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media as Model;

class Media extends Model implements HasMedia
{
    use InteractsWithMedia;

    public function isTemporaryUpload(): bool
    {
        return $this->model_type === app(TemporaryUpload::class)->getMorphClass();
    }
}
