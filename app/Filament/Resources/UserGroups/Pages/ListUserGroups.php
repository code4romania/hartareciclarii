<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserGroups\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\UserGroups\UserGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserGroups extends ListRecords
{
    protected static string $resource = UserGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
