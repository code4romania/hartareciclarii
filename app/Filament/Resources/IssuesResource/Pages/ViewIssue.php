<?php

declare(strict_types=1);

namespace App\Filament\Resources\IssuesResource\Pages;

// use Filament\Actions\Action;
use App\Filament\Resources\IssuesResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewIssue extends ViewRecord
{
    protected static string $resource = IssuesResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('issues.header.view', ['point_id' => $this->record->point_id]);
    }
}
