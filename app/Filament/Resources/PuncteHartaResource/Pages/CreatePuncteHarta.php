<?php

namespace App\Filament\Resources\PuncteHartaResource\Pages;

use App\Filament\Resources\PuncteHartaResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePuncteHarta extends CreateRecord
{
    protected static string $resource = PuncteHartaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
