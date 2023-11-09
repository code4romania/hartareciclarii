<?php

namespace App\Filament\Resources\IssuesResource\Pages;

use App\Enums\IssueStatus as IssueStatusEnum;
// use Filament\Actions\Action;
use App\Filament\Resources\IssuesResource;
use App\Filament\Resources\MapPointsResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ViewIssue extends ViewRecord
{
    protected static string $resource = IssuesResource::class;

    protected static string $view = 'filament.resources.issues-resource.pages.livewire';

    public $lat;

    public $lon;

    public $city;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    public function getSubHeading(): string | Htmlable
    {
        $record = $this->getRecord();
        $text = __('issues.labels.issue');
        if ($record->reporter)
        {
            $text .= __('issues.labels.reported_by', ['name'=>$record->reporter->fullname]);
        }
        $text .= __('issues.labels.reported_at', ['created_at'=>$record->created_at]) . '<br />';

        return new HtmlString($text);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_list')->label(__('issues.buttons.update_status'))->action(function ($data, $record)
            {
                $this->record->status = $data['status'];
                $this->record->save();
                Notification::make()
                    ->title(__('issues.status_updated'))
                    ->success()
                    ->send();
            })->form([
                Select::make('status')->options(IssueStatusEnum::toArray()),
            ])->color('success'),
            Action::make('view_list')->color('danger')->label(__('issues.buttons.view_map_point'))->url(MapPointsResource::getUrl('view', ['record'=>$this->record->map_point])),

        ];
    }

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? __('filament-panels::resources/pages/view-record.breadcrumb');
    }

    public function getTitle(): string | Htmlable
    {
        $record = $this->getRecord();

        $title = __('issues.labels.issue_with_point', ['point_id'=>$record->point_id]);

        return new HtmlString($title);
    }
}
