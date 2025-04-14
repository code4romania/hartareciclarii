<?php

declare(strict_types=1);

namespace App\Filament\Resources\ReportResource\Actions;

use App\Enums\Point\Status;
use App\Enums\ProblemStatus;
use App\Enums\ReportType;
use App\Jobs\Reports\PointsReports;
use App\Jobs\Reports\ProblemReports;
use App\Jobs\Reports\UserActivityReports;
use App\Models\City;
use App\Models\County;
use App\Models\Material;
use App\Models\PointType;
use App\Models\Problem\ProblemType;
use App\Models\Report;
use App\Models\ServiceType;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class GenerateReport extends Action
{
    public function getName(): ?string
    {
        return __('report.action.generate');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->label(__('report.action.generate'));
        $this->icon('heroicon-o-document-text');
        $this->color('primary');
        $this->action(fn (array $data) => $this->generateReport($data));
    }

    public function getForm(Form $form): ?Form
    {
        return $form->schema(
            [
                Hidden::make('created_by_id')
                    ->default(auth()->user()->id),
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
                Section::make(__('report.section.filter_dates_by'))
                    ->compact()
                    ->statePath('filters.structure')
                    ->schema(self::getUserActivityForm())
                    ->hidden(fn (Get $get) => blank($get('type')) || $get('type') !== ReportType::USER_ACTIVITY->value),

            ]
        );
    }

    private static function getUserActivityForm(): array
    {
        return [
            Group::make(
                [
                    CheckboxList::make('user_types')
                        ->label(__('report.column.user_type'))
                        ->default(['user', 'guest'])
                        ->options([
                            'user' => __('report.column.user'),
                            'guest' => __('report.column.guest'),
                        ]),
                    CheckboxList::make('contribution_type')
                        ->label(__('report.column.contribution_type'))
                        ->default([
                            'points',
                            'problems',
                        ])
                        ->options([
                            'points' => __('report.column.points'),
                            'problems' => __('report.column.problems'),
                        ]),
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

                ],
            )->columns(4),
            Radio::make('group_by')
                ->label(__('report.section.group_info_by'))
                ->columns(3)
                ->required()
                ->options([
                    'service_type_id' => __('report.column.service_type'),
                    'point_type_id' => __('report.column.point_type'),
                    'problem_type_id' => __('report.column.problem_type'),
                    'county_id' => __('report.column.county'),
                    'city_id' => __('report.column.city'),
                    'status' => __('report.column.status'),
                ]),
        ];
    }

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
                    'problem_type_id' => __('report.column.problem_type'),
                    'county_id' => __('report.column.county'),
                    'city_id' => __('report.column.city'),
                    'status' => __('report.column.status'),
                ]),
        ];
    }

    private function generateReport(array $data): void
    {
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
    }
}
