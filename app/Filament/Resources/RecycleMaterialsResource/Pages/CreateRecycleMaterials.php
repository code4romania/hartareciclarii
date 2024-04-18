<?php

declare(strict_types=1);

namespace App\Filament\Resources\RecycleMaterialsResource\Pages;

use App\Filament\Resources\RecycleMaterialsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRecycleMaterials extends CreateRecord
{
    protected static string $resource = RecycleMaterialsResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
