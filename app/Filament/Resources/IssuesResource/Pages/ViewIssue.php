<?php

declare(strict_types=1);

namespace App\Filament\Resources\IssuesResource\Pages;

// use Filament\Actions\Action;
use App\Enums\IssueStatus;
use App\Filament\Resources\IssuesResource;
use App\Filament\Resources\PointResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewIssue extends ViewRecord
{
    protected static string $resource = IssuesResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('issues.header.view', ['point_id' => $this->record->point_id]);
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('issues.subheader.view', ['user' => $this->record->user ?? __('issues.columns.no_user'), 'created_at' => $this->record->created_at]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label(__('issues.actions.delete')),
            Action::make('editPoint')->label(__('issues.actions.edit_point'))
                ->url(PointResource::getUrl('view', ['record' => $this->record->point_id]),shouldOpenInNewTab: true),
            Action::make('changeStatus')
                ->label(__('issues.actions.change_status'))
                ->form([
                    Select::make('status')
                        ->options(IssueStatus::options())
                        ->default($this->record->status->value),
                ])
                ->color('warning')
                ->action(
                    function (array $data) {
                        $this->record->changeStatus($data['status']);
                        Notification::make()
                            ->title(__('issues.notifications.status_changed'))
                            ->send();
                    }
                ),

        ];
    }
}
