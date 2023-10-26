<?php

declare(strict_types=1);

namespace App\Filament\Resources\ReportsResource\Pages;

use App\Contracts\Pages\WithTabs;
use App\Filament\Resources\ReportsResource;
use App\Filament\Resources\ReportsResource\Concerns;
use App\Models\County as CountyModel;
use App\Models\MapPoint as MapPointModel;
use App\Models\MapPointService as MapPointServiceModel;
use App\Models\MapPointType as MapPointTypeModel;
use App\Models\RecycleMaterial as RecycleMaterialModel;
use App\Models\Report;
use Filament\Actions\Action as FormAction;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
// use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Resources\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class GenerateReport extends Page implements HasForms, WithTabs, HasTable, HasActions
{
    use Concerns\HasTabs;

    use InteractsWithFormActions;

    use InteractsWithTable;

    public ?array $data = [];

    public ?Report $record = null;

    public $isGrouped = false;

    public $groupedBy = [];

    public $shouldGenerate = false;

    protected static string $resource = ReportsResource::class;

    protected static string $view = 'filament.resources.reports-resource.pages.generate';

    protected static bool $canCreateAnother = false;

    public function mount(): void
    {
        $this->authorizeAccess();

        $this->fillForm();
        $this->data['should_generate'] = false;
        $this->data['is_grouped'] = false;
    }

    protected function authorizeAccess(): void
    {
        static::authorizeResourceAccess();

        abort_unless(static::getResource()::canCreate(), 403);
    }

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        $this->form->fill();

        $this->callHook('afterFill');
    }

    public function generate()
    {
        $this->authorizeAccess();

        try
        {
            $this->isGrouped = false;
            if (\count($this->groupedBy) > 0)
            {
                $this->isGrouped = true;
            }

            $this->data = $this->form->getState();
            $this->shouldGenerate = true;
            // $data = $this->form->getState();
            // $this->record = $this->getModel()::make($data);
            // $this->form->model($this->record);
            // dd($this->record);

            // $this->report->model($this->record);
        }
        catch (Halt $exception)
        {
            return;
        }
    }

    protected function getForms(): array
    {
        return [
            'form',
            // 'report',
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filtreaza datele dupa: ')
                    ->schema([
                        Select::make('filters.service_type')
                            ->label(__('report.column.service_type'))
                            ->placeholder(__('report.placeholder.select_one'))
                            ->options(MapPointServiceModel::query()->pluck('display_name', 'id'))
                            ->preload(),
                        Select::make('filters.point_type')
                            ->label(__('report.column.point_type'))
                            ->placeholder(__('report.placeholder.select_one'))
                            ->options(MapPointTypeModel::query()->pluck('display_name', 'id'))
                            ->preload(),
                        Select::make('filters.materials')
                            ->label(__('report.column.materials'))
                            ->placeholder(__('report.placeholder.select_one'))
                            ->options(RecycleMaterialModel::query()->pluck('name', 'id'))
                            ->preload(),
                        Select::make('filters.location')
                            ->label(__('report.column.location'))
                            ->placeholder(__('report.placeholder.select_one'))
                            ->options(CountyModel::query()->pluck('name', 'id'))
                            ->preload(),
                        Select::make('filters.admin')
                            ->label(__('report.column.admin'))
                            ->placeholder(__('report.placeholder.select_one'))
                            ->options([0=>'Ce punem aici?'])
                            ->preload(),
                        Select::make('filters.status')
                            ->label(__('report.column.status'))
                            ->placeholder(__('report.placeholder.select_one'))
                            ->options([-1=>'Orice status', 0=>'Necesita verifcare', 1=>'Verificat'])
                            ->preload(),
                        DatePicker::make('filters.range.0')->label(__('report.column.range_start')),
                        DatePicker::make('filters.range.1')->label(__('report.column.range_end')),

                        Select::make('filters.fields')
                            ->label(__('report.column.fields'))
                            ->placeholder(__('report.placeholder.select_one'))
                            ->options([0=>'Ce punem aici?']),
                    ])
                    ->columns(4),
                Section::make('Grupeaza informatiile in tabel dupa: ')
                    ->schema([
                        Checkbox::make('group.service_type')
                            ->label(__('report.column.service_type'))
                            ->inline()
                            ->reactive()
                            ->afterStateUpdated(function ($state)
                            {
                                $this->updateGoupedBy($state, 'service_type');
                            }),
                        Checkbox::make('group.point_type')
                            ->label(__('report.column.point_type'))
                            ->inline()
                            ->reactive()
                            ->afterStateUpdated(function ($state)
                            {
                                $this->updateGoupedBy($state, 'point_type');
                            }),
                        Checkbox::make('group.materials')
                            ->label(__('report.column.materials'))
                            ->inline()
                            ->reactive()
                            ->afterStateUpdated(function ($state)
                            {
                                $this->updateGoupedBy($state, 'materials');
                            }),
                        Checkbox::make('group.city')
                            ->label(__('report.column.city'))
                            ->inline()
                            ->reactive()
                            ->afterStateUpdated(function ($state)
                            {
                                $this->updateGoupedBy($state, 'city');
                            }),
                        Checkbox::make('group.county')
                            ->label(__('report.column.county'))
                            ->inline()
                            ->reactive()
                            ->afterStateUpdated(function ($state)
                            {
                                $this->updateGoupedBy($state, 'county');
                            }),
                        Checkbox::make('group.admin')
                            ->label(__('report.column.admin'))
                            ->inline()
                            ->reactive()
                            ->afterStateUpdated(function ($state)
                            {
                                $this->updateGoupedBy($state, 'admin');
                            }),
                        Checkbox::make('group.status')
                            ->label(__('report.column.status'))
                            ->inline()
                            ->reactive()
                            ->afterStateUpdated(function ($state)
                            {
                                $this->updateGoupedBy($state, 'status');
                            }),
                        Checkbox::make('group.fields')
                            ->label(__('report.column.fields'))
                            ->inline()
                            ->reactive()
                            ->afterStateUpdated(function ($state)
                            {
                                $this->updateGoupedBy($state, 'fields');
                            }),
                    ])
                    ->columns(4),

            ])->statePath('data');
    }

    public function updateGoupedBy($state, $field)
    {
        if ($state)
        {
            $this->groupedBy[] = $field;
        }
        else
        {
            if (($key = array_search($field, $this->groupedBy)) !== false)
            {
                unset($this->groupedBy[$key]);
            }
        }
        $this->shouldGenerate = false;
        $this->isGrouped = false;
        if (\count($this->groupedBy) > 0)
        {
            $this->isGrouped = true;
        }
    }

    protected function getFormActions(): array
    {
        return [
            FormAction::make('create')
                ->label(__('report.action.generate'))
                ->keyBindings(['mod+s'])
                ->color('success')
                ->action(function ($data)
                {
                    return $this->generate();
                }),
            FormAction::make('cancel')
                ->label(__('report.action.reset'))
                ->url(static::getResource()::getUrl())
                ->color('primary'),

        ];
    }

    protected function getEloquentQuery(): Builder
    {
        $data = $this->data;
        if (!$this->shouldGenerate)
        {
            return Report::where('id', 0);
        }
        $query = MapPointModel::query();
        foreach ($data['filters'] as $filter =>$value)
        {
            if (\is_null($value))
            {
                continue;
            }

            switch($filter)
            {
                case 'service_type':
                    $query->where('service_id', $value);
                    break;
                case 'point_type':
                    $query->where('point_type_id', $value);
                    break;
                case 'materials':
                    $query->whereHas('materials', function ($q) use ($value)
                    {
                        $q->where('material_id', $value);
                    });
                    break;
                case 'location':
                    $query->whereHas('fields', function ($q) use ($value)
                    {
                        $q->where('field_type_id', 1);
                        $q->where('value', $value);
                    });
                    break;
                case 'admin':
                    break;
                case 'status':
                    if ($value != -1)
                    {
                        $query->where('status', $value);
                    }
                    break;
                case 'range':

                    if (!\is_null($value[0]) && !\is_null($value[1]))
                    {
                        $query->where('created_at', '<=', $value[1]);
                        $query->where('created_at', '>=', $value[0]);
                    }

                    break;
                case 'fields':
                    break;
            }
        }
        $select = ['recycling_points.*'];
        $hasGroup = false;
        foreach ($data['group'] as $group =>$value)
        {
            if (!$value)
            {
                continue;
            }
            $hasGroup = true;

            switch($group)
            {
                case 'service_type':
                    $query->groupBy('service_id');
                    break;
                case 'point_type':
                    $query->groupBy('point_type_id');
                    break;
                case 'materials':

                    $select[] = 'material_recycling_point.material_id as material_id';
                    $query->join('material_recycling_point', 'material_recycling_point.recycling_point_id', '=', 'recycling_points.id');
                    $query->groupBy('material_id');
                    break;
                case 'city':

                    $query->join('field_type_recycling_point as fields', function ($join)
                    {
                        $join->on('fields.recycling_point_id', '=', 'recycling_points.id')
                            ->where('fields.field_type_id', '1');
                    });
                    $select[] = 'fields.value as city_id';
                    $query->groupBy('fields.value');
                    break;
                case 'county':

                    $query->join('field_type_recycling_point as county', function ($join)
                    {
                        $join->on('county.recycling_point_id', '=', 'recycling_points.id')
                            ->where('county.field_type_id', '2');
                    });
                    $select[] = 'county.value as county_id';
                    $query->groupBy('county.value');
                    break;
                case 'admin':
                    break;
                case 'status':
                    $query->groupBy('status');
                    break;
                case 'fields':
                    break;
            }
        }
        if ($hasGroup)
        {
            $select[] = \DB::raw('COUNT("id") as total');
        }

        return $query->select($select)->with('issues', 'materials', 'fields', 'service', 'type');
    }

    public function getTableColumns(): array
    {
        if ($this->isGrouped)
        {
            return [
                TextColumn::make('created_at')->formatStateUsing(function (string $state, $record)
                {
                    return $record->created_at;
                })->html(),
            ];
        }
        else
        {
            return [
                TextColumn::make('id')->label(__('map_points.id'))->sortable()->searchable(),
                TextColumn::make('type.display_name')->label(__('map_points.point_type'))->searchable(),
                TextColumn::make('managed_by')->label(__('map_points.managed_by'))->sortable()->searchable()->wrap(),
                TextColumn::make('materials.getParent.icon')->label(__('map_points.materials'))->sortable()->searchable()
                    ->formatStateUsing(function (string $state, $record)
                    {
                        $icons = collect(explode(',', $state))->unique();
                        $state = '<div style="display:inline-flex">';
                        foreach ($icons as $icon)
                        {
                            $state .= __("<img style='width:30px;padding:5px' src='" . str_replace(' ', '', $icon) . "'>");
                        }
                        $state = rtrim($state) . '</div>';

                        return $state;
                    })->html(),
                TextColumn::make('county')->label(__('map_points.county'))->sortable()->searchable(),
                TextColumn::make('city')->label(__('map_points.city'))->sortable()->searchable(),
                TextColumn::make('address')->label(__('map_points.address'))->sortable()->searchable()->wrap(),
                TextColumn::make('group.name')->label(__('map_points.group'))->sortable()->searchable()->wrap(),

                BadgeColumn::make('status')
                    ->color(static function ($state, $record): string
                    {
                        if ($record->issues->count() > 0)
                        {
                            return 'danger';
                        }
                        if ((int) $state === 1)
                        {
                            return 'success';
                        }

                        return 'warning';
                    })
                    ->formatStateUsing(function (string $state, $record)
                    {
                        if ($record->issues->count() > 0)
                        {
                            return __('map_points.issues_found');
                        }
                        if ((int) $state === 1)
                        {
                            return __('map_points.verified');
                        }

                        return __('map_points.requires_verification');
                    })->html(),

            ];
        }
    }

    public function table(Table $table): Table
    {
        $data = $this->data;
        $actions = [
            TableAction::make('save-report')->label(__('report.action.save_report'))->icon('heroicon-m-check')
                ->form([
                    TextInput::make('title')
                        ->label('Titlu raport')
                        ->required(),
                ])
                ->action(function (array $data): void
                {
                    $report = new Report();
                    $report->form_data = $this->data;
                    $report->results = $this->getEloquentQuery()->get();
                    $report->title = $data['title'];
                    $report->save();
                    Notification::make()
                        ->title('Report saved')
                        ->success()
                        ->send();
                })
                ->visible(function ($record): bool
                {
                    return $this->shouldGenerate;
                }),
            ExportAction::make()->exports([
                ExcelExport::make('table')->fromTable()
                    ->except([
                        'materials.getParent.icon',
                    ])
                    ->withColumns([
                        Column::make('materials')
                            ->formatStateUsing(function ($state)
                            {
                                $records = collect($state);

                                return implode(',', $records->pluck('name')->toArray());
                            }),
                    ]),

            ])->visible(function ($record): bool
            {
                return $this->shouldGenerate;
            }),

        ];

        return $table
            ->query(function ()
            {
                return $this->getEloquentQuery()->limit(100);
            })
            ->headerActions(
                $actions,
            )
            ->columns($this->getTableColumns())
            // ->view('filament.resources.reports-resource.pages.view', ['data'=>$this->data]);
            ->view('filament-tables::index');
    }
}
