<?php

declare(strict_types=1);

namespace App\Filament\Resources\RecycleMaterials\Pages;

use App\Filament\Resources\RecycleMaterials\RecycleMaterialsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRecycleMaterials extends EditRecord
{
    protected static string $resource = RecycleMaterialsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
