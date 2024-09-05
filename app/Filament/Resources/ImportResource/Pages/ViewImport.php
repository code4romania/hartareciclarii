<?php

declare(strict_types=1);

namespace App\Filament\Resources\ImportResource\Pages;

use App\Filament\Resources\ImportResource;
use App\Models\ImportExport as ImportExportModel;
use App\Models\MapPoint as MapPointModel;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ViewImport extends ViewRecord implements HasTable, HasActions
{
    use InteractsWithTable;

    protected static string $resource = ImportResource::class;

    protected static string $view = 'filament.resources.import-resource.pages.view-report';

    public $view_type = 'processed';

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->view_type = app()->request->get('show', 'processed');
        $data = $this->getRecord()->result[$this->view_type];

        return $data;
    }

    public function getSubHeading(): string | Htmlable
    {
        return \sprintf(__('imports.sub_heading'), $this->getRecord()->createdBy->fullname, $this->getRecord()->created_at, $this->getRecord()->finished_at);
    }

    protected function getHeaderActions(): array
    {
        $actions = [];
        $record = $this->getRecord();
        $failed = (isset($record->result['failed'])) ? \count($record->result['failed']) : 0;
        $processed = (isset($record->result['processed'])) ? \count($record->result['processed']) : 0;
        $actions = array_merge($actions, [
            Action::make('failed')
                ->label(\sprintf(__('imports.not_imported'), $failed))
                ->icon('heroicon-m-check')
                ->hidden(function () use ($failed) {
                    return $this->view_type == 'failed' || $failed == 0;
                })
                ->url(fn (ImportExportModel $record): string => ImportResource::getUrl('view_report', ['record' => $record->id]) . '?show=failed')
                ->color('danger'),
            Action::make('processed')
                ->label(\sprintf(__('imports.imported'), $processed))
                ->icon('heroicon-m-check')
                ->hidden(function () use ($processed) {
                    return $this->view_type == 'processed' || $processed == 0;
                })
                ->url(fn (ImportExportModel $record): string => ImportResource::getUrl('view_report', ['record' => $record->id]) . '?show=processed')
                ->color('success'),
        ]);

        return $actions;
    }

    protected function getPostFormSchema(): array
    {
        return [
            TextInput::make('title')
                ->required(),
        ];
    }

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? __('filament-panels::resources/pages/view-record.breadcrumb');
    }

    public function getContentTabLabel(): ?string
    {
        return __('filament-panels::resources/pages/view-record.content.tab.label');
    }

    public function getTitle(): string | Htmlable
    {
        $record = $this->getRecord();

        $title = __('imports.import_record_label') . $this->getRecord()->id;

        return new HtmlString($title);
    }

    public function table(Table $table): Table
    {
        switch($this->view_type) {
            case 'processed':
                return $this->getProccesedTable($table);
                break;
            case 'failed':
                return $this->getFailedTable($table);
                break;
        }
    }

    public function getProccesedTable(Table $table): Table
    {
        return $table
            ->query(MapPointModel::query()->whereIn('id', array_values($this->data)))
            ->columns([

                TextColumn::make('id')
                    ->label(__('map_points.id'))
                    ->formatStateUsing(function ($state) {
                        return '<a href="' . \App\Filament\Resources\PointResource::getUrl('view', ['record' => $state]) . '">' . $state . '</a>';
                    })
                    ->sortable()
                    ->searchable()
                    ->html(),
                TextColumn::make('type.display_name')
                    ->label(__('map_points.point_type'))
                    ->searchable(),
                TextColumn::make('managed_by')
                    ->label(__('map_points.managed_by'))
                    ->sortable()
                    ->searchable()
                    ->wrap(),
                TextColumn::make('materials.getParent.icon')
                    ->label(__('map_points.materials'))
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function (string $state, $record) {
                        $icons = collect(explode(',', $state))->unique();
                        $state = '<div style="display:inline-flex">';
                        foreach ($icons as $icon) {
                            $state .= __("<img style='width:30px;padding:5px' src='" . str_replace(' ', '', $icon) . "'>");
                        }
                        $state = rtrim($state) . '</div>';

                        return $state;
                    })
                    ->html(),
                TextColumn::make('county')
                    ->label(__('map_points.county'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('city')
                    ->label(__('map_points.city'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('address')
                    ->label(__('map_points.address'))
                    ->sortable()
                    ->searchable()
                    ->wrap(),
                TextColumn::make('group.name')
                    ->label(__('map_points.group'))
                    ->sortable()
                    ->searchable()
                    ->wrap(),

                BadgeColumn::make('status')
                    ->color(static function ($state, $record): string {
                        if ($record->issues->count() > 0) {
                            return 'danger';
                        }
                        if ((int) $state === 1) {
                            return 'success';
                        }

                        return 'warning';
                    })
                    ->formatStateUsing(function (string $state, $record) {
                        if ($record->issues->count() > 0) {
                            return __('map_points.issues_found');
                        }
                        if ((int) $state === 1) {
                            return __('map_points.verified');
                        }

                        return __('map_points.requires_verification');
                    })->html(),
            ]);
    }

    public function getFailedTable($table): Table
    {
        return $table
            ->query(ImportExportModel::query())
            ->columns([

            ]);

        return $table;
    }
}
