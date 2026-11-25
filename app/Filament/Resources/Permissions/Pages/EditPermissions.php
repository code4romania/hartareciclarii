<?php

declare(strict_types=1);

namespace App\Filament\Resources\Permissions\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Permissions\PermissionsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermissions extends EditRecord
{
    protected static string $resource = PermissionsResource::class;

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
