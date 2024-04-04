<?php

declare(strict_types=1);

namespace App\Filament\Resources\DuplicatesResource\Pages;

use App\Filament\Resources\DuplicatesResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDuplicates extends CreateRecord
{
    protected static string $resource = DuplicatesResource::class;
}
