<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\Media;
use Spatie\MediaLibrary\HasMedia;

trait CanAttachMedia
{
    protected function attachMedia(HasMedia $model, ?array $uuids): void
    {
        if (blank($uuids)) {
            return;
        }

        Media::query()
            ->whereIn('uuid', $uuids)
            ->update([
                'model_id' => $model->getKey(),
                'model_type' => $model->getMorphClass(),
            ]);
    }
}
