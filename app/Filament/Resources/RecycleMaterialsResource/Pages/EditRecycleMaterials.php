<?php

declare(strict_types=1);

namespace App\Filament\Resources\RecycleMaterialsResource\Pages;

use App\Filament\Resources\RecycleMaterialsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecycleMaterials extends EditRecord
{
    protected static string $resource = RecycleMaterialsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
