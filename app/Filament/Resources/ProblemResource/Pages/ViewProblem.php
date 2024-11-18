<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProblemResource\Pages;

// use Filament\Actions\Action;
use App\Enums\IssueStatus;
use App\Filament\Resources\PointResource;
use App\Filament\Resources\ProblemResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewProblem extends ViewRecord
{
    protected static string $resource = ProblemResource::class;

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
            Action::make('changeStatus')
                ->label(__('issues.actions.change_status'))
                ->form([
                    Select::make('status')
                        ->options(IssueStatus::options())
                        ->default($this->record->status->value),
                ])
                ->color('primary')
                ->action(
                    function (array $data) {
                        $this->record->changeStatus($data['status']);
                        Notification::make()
                            ->title(__('issues.notifications.status_changed'))
                            ->send();
                    }
                ),

            Action::make('editPoint')
                ->label(__('issues.actions.edit_point'))
                ->outlined()
                ->url(PointResource::getUrl('view', ['record' => $this->record->point_id]), shouldOpenInNewTab: true),

            DeleteAction::make()
                ->label(__('issues.actions.delete'))
                ->action(function (){
                    $this->record->contribution()->delete();
                    $this->record->delete();
                    $this->redirect(ProblemResource::getUrl('index'));
                })
//                ->successRedirectUrl(ProblemResource::getUrl('index'))
                ->outlined(),

        ];
    }
}
