<?php

declare(strict_types=1);

namespace App\Filament\Resources\Roles\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Roles\RolesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RolesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
