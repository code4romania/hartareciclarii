<?php

namespace App\Filament\Resources\MapPointsResource\Pages;

use App\Filament\Resources\MapPointsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMapPoints extends EditRecord
{
    protected static string $resource = MapPointsResource::class;

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
