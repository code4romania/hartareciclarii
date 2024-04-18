<?php

declare(strict_types=1);

/*
 * @Author: Bogdan Bocioaca
 * @Date:   2023-10-31 11:05:30
 * @Last Modified by:   Bogdan Bocioaca
 * @Last Modified time: 2023-11-01 10:51:29
 */

namespace App\Filament\Resources\ReportsResource\Traits;

use App\Enums\MapPointTypes;
use App\Exports\ReportsExport;
use App\Models\County as CountyModel;
// use Filament\Resources\Pages\CreateRecord;
use App\Models\IssueType as IssueTypeModel;
use App\Models\MapPoint as MapPointModel;
use App\Models\MapPointService as MapPointServiceModel;
use App\Models\MapPointToField as MapPointToFieldModel;
use App\Models\MapPointType as MapPointTypeModel;
use App\Models\RecycleMaterial as RecycleMaterialModel;
use App\Models\Report;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
// use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

trait UsersReportTrait
{
    public function getUsersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('report.section.filter_date_range'))
                    ->schema([

                        DatePicker::make('filters.range.0')
                            ->label(__('report.column.range_start')),
                        DatePicker::make('filters.range.1')
                            ->label(__('report.column.range_end')),
                    ])
                    ->columns(2),
                Section::make(__('report.section.filter_dates_by'))
                    ->schema([
                        Select::make('filters.user_type')
                            ->label(__('report.column.user_type'))
                            ->placeholder(__('report.placeholder.select_one'))
                            ->options(__('report.column.users_type'))
                            ->reactive()
                            ->preload(),
                        Select::make('filters.activity_type')
                            ->label(__('report.column.activity_type'))
                            ->placeholder(__('report.placeholder.select_one'))
                            ->options(__('report.column.activities_options'))
                            ->reactive()
                            ->preload(),
                        Select::make('filters.county')
                            ->label(__('report.column.county'))
                            ->placeholder(__('report.placeholder.select_one'))
                            ->options(CountyModel::query()->pluck('name', 'id'))
                            ->multiple()
                            ->reactive()
                            ->preload(),
                        Select::make('filters.city')
                            ->label(__('report.column.city'))
                            ->placeholder(__('report.placeholder.select_one'))
                            ->options(MapPointToFieldModel::select('value')->where('field_type_id', MapPointTypes::City)->distinct()->orderBy('value', 'ASC')->get()->pluck('value'))
                            ->multiple()
                            ->reactive()
                            ->preload(),

                    ])
                    ->columns(4),
                Section::make(__('report.section.group_info_by'))
                    ->schema([
                        Radio::make('group')
                            ->options([
                                'service_type' => __('report.column.service_type'),
                                'point_type' => __('report.column.point_type'),
                                'county' => __('report.column.county'),
                                'city' => __('report.column.city'),
                                'status' => __('report.column.issue_status'),
                                'type' => __('report.column.issue_type'),
                                'points_added' => __('report.column.points_added'),
                                'issues_added' => __('report.column.issues_added'),
                            ])->afterStateUpdated(function ($state) {
                                $this->updateGoupedBy($state);
                            })
                            ->required(),
                    ])
                    ->columns(4),
                Hidden::make('type')->default(request()->get('type')),

            ])->statePath('data');
    }

    protected function getUsersEloquentQuery(): Builder
    {
        $data = $this->data;
        if (! $this->shouldGenerate) {
            return Report::where('id', 0);
        }
        $query = MapPointModel::query()->leftJoin('reported_point_issues', 'reported_point_issues.point_id', '=', 'recycling_points.id');

        foreach ($data['filters'] as $filter => $value) {
            if (\is_null($value) || (\is_array($value) && \count($value) == 0)) {
                continue;
            }
            switch($filter) {
                case 'user_type':
                    switch($value) {
                        case '-1':
                            $query->where(function ($q) {
                                $q->where('created_by', '>', 0)->orWhereNull('created_by');
                            });
                            break;
                        case '0':
                            $query->where('created_by', '>', 0);
                            break;
                        case '1':
                            $query->where(function ($q) {
                                $q->where('created_by', '=', 0)->orWhereNull('created_by');
                            });
                            break;
                    }
                    break;
                case 'point_type':
                    $query->whereIn('point_type_id', $value);
                    break;
                case 'city':
                    $query->whereHas('fields', function ($q) use ($value) {
                        $q->where('field_type_id', MapPointTypes::City);
                        $q->whereIn('value', $value);
                    });
                    break;
                case 'county':
                    $query->whereHas('fields', function ($q) use ($value) {
                        $q->where('field_type_id', MapPointTypes::County);
                        $q->whereIn('value', $value);
                    });
                    break;
                case 'range':

                    if (! \is_null($value[0]) && ! \is_null($value[1])) {
                        $query->where('created_at', '<=', $value[1]);
                        $query->where('created_at', '>=', $value[0]);
                    }
                    break;
                default:
                    break;
            }
        }
        $select = [
            'recycling_points.*',
            \DB::raw('(CASE WHEN  reported_point_issues.id IS NULL THEN 0 ELSE 1 END) AS reported_issue_type'),
            \DB::raw('(CASE WHEN  recycling_points.id IS NULL THEN 0 ELSE 1 END) AS recycling_point_type'),

        ];

        switch($this->groupedBy) {
            case 'service_type':
                $query->join('recycling_point_services', 'recycling_points.service_id', '=', 'recycling_point_services.id');
                $select[] = 'recycling_point_services.display_name as grouped_by';
                $query->groupBy('recycling_points.service_id');
                break;
            case 'point_type':
                $query->join('recycling_point_types', 'recycling_points.point_type_id', '=', 'recycling_point_types.id');
                $select[] = 'recycling_point_types.display_name as grouped_by';

                $query->groupBy('point_type_id');
                break;
            case 'materials':

                $select[] = 'material_recycling_point.material_id as material_id';
                $select[] = 'materials.name as grouped_by';
                $query->join('material_recycling_point', 'material_recycling_point.recycling_point_id', '=', 'recycling_points.id');
                $query->join('materials', 'material_recycling_point.material_id', '=', 'materials.id');
                $query->groupBy('material_id');
                break;
            case 'city':

                $query->join('field_type_recycling_point as fields', function ($join) {
                    $join->on('fields.recycling_point_id', '=', 'recycling_points.id')
                        ->where('fields.field_type_id', MapPointTypes::City);
                });
                $select[] = 'fields.value as city_id';
                $select[] = 'fields.value as grouped_by';
                $query->groupBy('fields.value');
                break;
            case 'county':

                $query->join('field_type_recycling_point as county', function ($join) {
                    $join->on('county.recycling_point_id', '=', 'recycling_points.id')
                        ->where('county.field_type_id', MapPointTypes::County);
                });
                $select[] = 'county.value as county_id';
                $select[] = 'county.value as grouped_by';
                $query->groupBy('county.value');
                break;
            case 'status':
                $select[] = 'reported_point_issues.status as grouped_by';
                $query->groupBy('status');
                break;
            case 'type':
                $query->join('reported_point_issue_types', 'reported_point_issues.reported_point_issue_type_id', '=', 'reported_point_issue_types.id');
                $select[] = 'reported_point_issue_types.title as grouped_by';
                $query->groupBy('reported_point_issue_types.id');
                break;
            case 'points_added':
                $select[] = \DB::raw('"points_added" as grouped_by');
                $query->groupBy('recycling_points.created_by');
                break;
            case 'issues_added':
                $select[] = \DB::raw('"issues_added" as grouped_by');
                $query->groupBy('reported_point_issues.reporter_id');
                break;
            default:
                break;
        }
        if (isset($data['filters']['activity_type'])) {
            switch($data['filters']['activity_type']) {
                case '0':
                    $query->having('recycling_point_type', 1);
                    break;
                case '1':
                    $query->having('reported_issue_type', 1);

                    break;
                default:

                    break;
            }
        }
        $select[] = \DB::raw('COUNT(*) as total');

        $query->select($select)->with('issues', 'materials', 'fields', 'service', 'type');

        return $query;
    }

    public function getUsersTableColumns(): array
    {
        $header = [];
        if (\is_null($this->groupedBy)) {
            return $header;
        }
        switch($this->groupedBy) {
            case 'service_type':
                $header = MapPointServiceModel::all()->pluck('display_name');
                break;
            case 'point_type':
                $header = MapPointTypeModel::all()->pluck('display_name');
                break;
            case 'materials':
                $header = RecycleMaterialModel::all()->pluck('name');
                break;
            case 'city':
                $header = MapPointToFieldModel::where('field_type_id', MapPointTypes::City)->groupBy('value')->get()->pluck('value');
                break;
            case 'county':
                $header = CountyModel::all()->pluck('name');
                break;
            case 'status':
                $header = __('report.column.issues_options');
                break;
            case 'type':
                $header = IssueTypeModel::all()->pluck('title');
                break;
            case 'points_added':
            case 'issues_added':
                $header = [
                    '0-10',
                    '11-20',
                    '21-30',
                    '31-40',
                    '41-50',
                    '51-60',
                    '61-70',
                    '71-80',
                    '81-90',
                    '91-100',
                    __('report.column.over_100'),
                ];
                break;
            default:
                break;
        }
        $columns = [];
        if (\count($header)) {
            foreach ($header as $head) {
                $columns[] = TextColumn::make($head)->label($head)->html();
            }
        }

        return $columns;
    }

    public function getUsersTable(Table $table): Table
    {
        $data = $this->data;
        $actions = [
            TableAction::make('save-report')->label(__('report.action.save_report'))->icon('heroicon-m-check')
                ->form([
                    TextInput::make('title')
                        ->label(__('report.column.title'))
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $report = new Report();
                    $report->form_data = $this->data;
                    $report->results = $this->formatResults($this->getUsersEloquentQuery()->get());
                    $report->title = $data['title'];
                    $report->type = 'issues';
                    $report->save();
                    Notification::make()
                        ->title(__('report.action.saved'))
                        ->success()
                        ->send();
                })
                ->visible(function ($record): bool {
                    return $this->shouldGenerate;
                }),
            TableAction::make('export_report')
                ->label(__('report.action.export'))
                ->icon('heroicon-m-arrow-down-tray')
                ->form([
                    TextInput::make('title')
                        ->label(__('report.column.title'))
                        ->required(),
                ])
                ->action(function (array $data) {
                    return Excel::download(new ReportsExport($this->getUsersEloquentQuery()->get(), $this->getTableColumns()), $data['title'] . '.xlsx');
                })
                ->visible(function ($record): bool {
                    return $this->shouldGenerate;
                }),

        ];

        return $table
            ->query(function () {
                return $this->getUsersEloquentQuery()->limit(100);
            })
            ->headerActions(
                $actions,
            )
            ->paginated(false)
            ->columns($this->getTableColumns())
            ->view('filament.resources.reports-resource.pages.view', [
                'data' => $this->data,
                'header' => $this->getTableColumns(),
            ]);
        // ->view('filament-tables::index');
    }
}
