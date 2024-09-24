<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProblemResource\Pages;

use App\Filament\Resources\ProblemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProblem extends CreateRecord
{
    protected static string $resource = ProblemResource::class;
}
