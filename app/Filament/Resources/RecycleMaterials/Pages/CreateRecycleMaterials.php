<?php

declare(strict_types=1);

namespace App\Filament\Resources\RecycleMaterials\Pages;

use App\Filament\Resources\RecycleMaterials\RecycleMaterialsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRecycleMaterials extends CreateRecord
{
    protected static string $resource = RecycleMaterialsResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
