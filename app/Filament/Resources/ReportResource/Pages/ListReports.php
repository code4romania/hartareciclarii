<?php

declare(strict_types=1);

namespace App\Filament\Resources\ReportResource\Pages;

use App\Enums\Point\Status;
use App\Enums\ProblemStatus;
use App\Enums\ReportType;
use App\Filament\Resources\ReportResource;
use App\Jobs\Reports\PointsReports;
use App\Jobs\Reports\ProblemReports;
use App\Models\City;
use App\Models\County;
use App\Models\Material;
use App\Models\PointType;
use App\Models\Problem\ProblemType;
use App\Models\Report;
use App\Models\ServiceType;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListReports extends ListRecords
{
    protected static string $resource = ReportResource::class;

    private static function getPointForm(): array
    {
        return[
            Group::make(
                [
                    Select::make('service_ids')
                        ->label(__('report.column.service_type'))
                        ->multiple()
                        ->searchable()
                        ->live()
                        ->placeholder(__('report.placeholder.service_type'))
                        ->options(ServiceType::query()->whereHas('points')->pluck('name', 'id')),

                    Select::make('point_type_ids')
                        ->label(__('report.column.point_type'))
                        ->multiple()
                        ->searchable()
                        ->placeholder(__('report.placeholder.point_type'))
                        ->options(fn (Get $get) => PointType::query()
                            ->when($get('service_ids'), fn (Builder $query) => $query->whereIn('service_type_id', $get('service_ids')))
                            ->whereHas('points')
                            ->pluck('name', 'id')),

                    Select::make('material_ids')
                        ->label(__('report.column.materials'))
                        ->multiple()
                        ->searchable()
                        ->placeholder(__('report.placeholder.materials'))
                        ->options(fn (Get $get) => Material::query()->whereHas('points')->pluck('name', 'id'))
                        ->hidden(function (Get $get) {
                            if (blank($get('service_ids'))) {
                                return true;
                            }

                            return ! ServiceType::query()->whereIn('id', $get('service_ids'))->where('can_collect_materials', true)->exists();
                        }),

                ]
            )->columns(3),

            Group::make([
                Select::make('county_ids')
                    ->label(__('report.column.county'))
                    ->multiple()
                    ->live()
                    ->searchable()
                    ->placeholder(__('report.placeholder.county'))
                    ->options(fn (Get $get) => County::query()
                        ->pluck('name', 'id')),

                Select::make('city_ids')
                    ->label(__('report.column.city'))
                    ->multiple()
                    ->searchable()
                    ->placeholder(__('report.placeholder.city'))
                    ->options(fn (Get $get) => City::query()
                        ->when($get('county_ids'), fn (Builder $query) => $query->whereIn('county_id', $get('county_ids')))
                        ->pluck('name', 'id')),

            ])->columns(2),

            Select::make('status')
                ->label(__('report.column.status'))
                ->multiple()
                ->searchable()
                ->placeholder(__('report.placeholder.status'))
                ->options(Status::options()),
            Radio::make('group_by')
                ->label(__('report.section.group_info_by'))
                ->columns(3)
                ->required()
                ->options([
                    'service_type_id' => __('report.column.service_type'),
                    'point_type_id' => __('report.column.point_type'),
                    'materials' => __('report.column.materials'),
                    'county_id' => __('report.column.county'),
                    'city_id' => __('report.column.city'),
                    'status' => __('report.column.status'),
                ]),
        ];
    }

    private static function getProblemForm(): array
    {
        return[
            Group::make(
                [
                    Select::make('service_ids')
                        ->label(__('report.column.service_type'))
                        ->multiple()
                        ->searchable()
                        ->live()
                        ->placeholder(__('report.placeholder.service_type'))
                        ->options(ServiceType::query()->whereHas('points', fn (Builder $query) => $query->whereHas('problems'))->pluck('name', 'id')),

                    Select::make('point_type_ids')
                        ->label(__('report.column.point_type'))
                        ->multiple()
                        ->searchable()
                        ->placeholder(__('report.placeholder.point_type'))
                        ->options(fn (Get $get) => PointType::query()
                            ->when($get('service_ids'), fn (Builder $query) => $query->whereIn('service_type_id', $get('service_ids')))
                            ->whereHas('points', fn (Builder $query) => $query->whereHas('problems'))
                            ->pluck('name', 'id')),

                    Select::make('problem_type_ids')
                        ->label(__('report.column.issue_type'))
                        ->multiple()
                        ->searchable()
                        ->placeholder(__('report.placeholder.issue_type'))
                        ->options(fn (Get $get) => ProblemType::query()
                            ->when($get('service_ids'), fn (Builder $query) => $query->whereHas('serviceTypes', fn (Builder $query) => $query->whereIn('service_type_id', $get('service_ids'))))
                            ->pluck('name', 'id')),

                ]
            )->columns(3),

            Group::make([
                Select::make('county_ids')
                    ->label(__('report.column.county'))
                    ->multiple()
                    ->live()
                    ->searchable()
                    ->placeholder(__('report.placeholder.county'))
                    ->options(fn (Get $get) => County::query()
                        ->pluck('name', 'id')),

                Select::make('city_ids')
                    ->label(__('report.column.city'))
                    ->multiple()
                    ->searchable()
                    ->placeholder(__('report.placeholder.city'))
                    ->options(fn (Get $get) => City::query()
                        ->when($get('county_ids'), fn (Builder $query) => $query->whereIn('county_id', $get('county_ids')))
                        ->pluck('name', 'id')),

            ])->columns(2),

            Select::make('status')
                ->label(__('report.column.status'))
                ->multiple()
                ->searchable()
                ->placeholder(__('report.placeholder.status'))
                ->options(ProblemStatus::options()),

            Radio::make('group_by')
                ->label(__('report.section.group_info_by'))
                ->columns(3)
                ->required()
                ->options([
                    'service_type_id' => __('report.column.service_type'),
                    'point_type_id' => __('report.column.point_type'),
                    'materials' => __('report.column.materials'),
                    'county_id' => __('report.column.county'),
                    'city_id' => __('report.column.city'),
                    'status' => __('report.column.status'),
                ]),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('add_report')
                ->label(__('report.action.generate'))
                ->form(
                    [
                        Select::make('type')
                            ->options(ReportType::options())
                            ->required()
                            ->label(__('report.type'))
                            ->live(),
                        Group::make(
                            [
                                DatePicker::make('start_date')
                                    ->label(__('report.column.range_start'))
                                    ->required(),

                                DatePicker::make('end_date')
                                    ->label(__('report.column.range_end'))
                                    ->required(),
                            ]
                        )->columns(2)->statePath('filters'),

                        Section::make(__('report.section.filter_dates_by'))
                            ->compact()
                            ->statePath('filters.structure')
                            ->schema(self::getPointForm())
                            ->hidden(fn (Get $get) => blank($get('type')) || $get('type') !== ReportType::POINTS->value),
                        Section::make(__('report.section.filter_dates_by'))
                            ->compact()
                            ->statePath('filters.structure')
                            ->schema(self::getProblemForm())
                            ->hidden(fn (Get $get) => blank($get('type')) || $get('type') !== ReportType::PROBLEMS->value),

                    ]
                )->action(function (array $data) {
                    $data['created_by'] = auth()->user()->id;
                    $report = Report::create($data);
                    $type = ReportType::tryFrom($report->type);
                    match ($type) {
                        ReportType::POINTS => PointsReports::dispatch($report),
                        ReportType::PROBLEMS => ProblemReports::dispatch($report),
                        ReportType::USER_ACTIVITY => UserActivityReports::dispatch($report),
                        ReportType::TOP_USERS => TopUsersReports::dispatch($report),
                    };
                    Notification::make()
                        ->title(__('report.notification.started.title'))
                        ->body(__('report.notification.started.body'))
                        ->send();
                }),
        ];
    }

    public function getTabs(): array
    {
        return collect(ReportType::options())
            ->map(
                fn ($label, $value) => Tab::make($label)->query(fn (Builder $query) => $query->where('type', $value))
            )
            ->toArray();
    }
}
