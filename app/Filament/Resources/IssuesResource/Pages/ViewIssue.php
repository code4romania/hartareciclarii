<?php

declare(strict_types=1);

namespace App\Filament\Resources\IssuesResource\Pages;

// use Filament\Actions\Action;
use App\Filament\Resources\IssuesResource;
use App\Filament\Resources\MapPointsResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\ActionSize;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;

class ViewIssue extends ViewRecord
{
    protected static string $resource = IssuesResource::class;


    public function getTitle(): string|Htmlable
    {
        return __('issues.header.view',['point_id' => $this->record->point_id]);
    }

}
