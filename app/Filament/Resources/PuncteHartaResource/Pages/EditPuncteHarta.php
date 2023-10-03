<?php

namespace App\Filament\Resources\PuncteHartaResource\Pages;

use App\Filament\Resources\PuncteHartaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPuncteHarta extends EditRecord
{
    protected static string $resource = PuncteHartaResource::class;

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
