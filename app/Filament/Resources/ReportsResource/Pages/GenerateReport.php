<?php

declare(strict_types=1);

namespace App\Filament\Resources\ReportsResource\Pages;

use App\Contracts\Pages\WithTabs;
use App\Filament\Resources\ReportsResource;
use App\Filament\Resources\ReportsResource\Concerns;
use App\Models\County as CountyModel;
use App\Models\MapPoint as MapPointModel;
use App\Models\MapPointService as MapPointServiceModel;
use App\Models\MapPointToField as MapPointToFieldModel;
use App\Models\MapPointType as MapPointTypeModel;
use App\Models\RecycleMaterial as RecycleMaterialModel;
use App\Models\Report;
use Filament\Actions\Action as FormAction;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
// use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Resources\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class GenerateReport extends Page implements HasForms, WithTabs, HasTable, HasActions
{
    use Concerns\HasTabs;

    use InteractsWithFormActions;

    use InteractsWithTable;

    public ?array $data = [];

    public ?Report $record = null;

    public $isGrouped = false;

    public $groupedBy = null;

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
                            ->multiple()
                            ->reactive()
                            ->preload(),
                        Select::make('filters.point_type')
                            ->label(__('report.column.point_type'))
                            ->placeholder(__('report.placeholder.select_one'))
                            ->options(MapPointTypeModel::query()->pluck('display_name', 'id'))
                            ->multiple()
                            ->reactive()
                            ->preload(),
                        Select::make('filters.materials')
                            ->label(__('report.column.materials'))
                            ->placeholder(__('report.placeholder.select_one'))
                            ->options(RecycleMaterialModel::query()->pluck('name', 'id'))
                            ->multiple()
                            ->reactive()
                            ->preload(),
                        Select::make('filters.location')
                            ->label(__('report.column.location'))
                            ->placeholder(__('report.placeholder.select_one'))
                            ->options(CountyModel::query()->pluck('name', 'id'))
                            ->multiple()
                            ->reactive()
                            ->preload(),
                        Select::make('filters.status')
                            ->label(__('report.column.status'))
                            ->placeholder(__('report.placeholder.select_one'))
                            ->options([-1=>'Orice status', 0=>'Necesita verifcare', 1=>'Verificat'])
                            ->multiple()
                            ->reactive()
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
                        Radio::make('group')
                            ->options([
                                'service_type'=>__('report.column.service_type'),
                                'point_type'=>__('report.column.point_type'),
                                'materials'=>__('report.column.materials'),
                                'city'=>__('report.column.city'),
                                'county'=>__('report.column.county'),
                                'status'=>__('report.column.status'),
                                'fields'=>__('report.column.fields'),
                            ])->afterStateUpdated(function ($state)
                            {
                                $this->updateGoupedBy($state);
                            })
                            ->required(),
                    ])
                    ->columns(4),

            ])->statePath('data');
    }

    public function updateGoupedBy($field)
    {
        $this->groupedBy = $field;
        $this->shouldGenerate = false;
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
            if (\is_null($value) || (\is_array($value) && \count($value) == 0))
            {
                continue;
            }
            switch($filter)
            {
                case 'service_type':
                    $query->whereIn('service_id', $value);
                    break;
                case 'point_type':
                    $query->whereIn('point_type_id', $value);
                    break;
                case 'materials':
                    $query->whereHas('materials', function ($q) use ($value)
                    {
                        $q->whereIn('material_id', $value);
                    });
                    break;
                case 'location':
                    $query->whereHas('fields', function ($q) use ($value)
                    {
                        $q->where('field_type_id', 1);
                        $q->whereIn('value', $value);
                    });
                    break;
                case 'admin':
                    break;
                case 'status':
                    if ($value != -1)
                    {
                        $query->whereIn('status', $value);
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

        switch($this->groupedBy)
        {
            case 'service_type':
                $query->join('recycling_point_services', 'recycling_points.service_id', '=', 'recycling_point_services.id');
                $select[] = 'recycling_point_services.display_name as grouped_by';
                $query->groupBy('service_id');
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

                $query->join('field_type_recycling_point as fields', function ($join)
                {
                    $join->on('fields.recycling_point_id', '=', 'recycling_points.id')
                        ->where('fields.field_type_id', '1');
                });
                $select[] = 'fields.value as city_id';
                $select[] = 'fields.value as grouped_by';
                $query->groupBy('fields.value');
                break;
            case 'county':

                $query->join('field_type_recycling_point as county', function ($join)
                {
                    $join->on('county.recycling_point_id', '=', 'recycling_points.id')
                        ->where('county.field_type_id', '2');
                });
                $select[] = 'county.value as county_id';
                $select[] = 'county.value as grouped_by';
                $query->groupBy('county.value');
                break;
            case 'admin':
                break;
            case 'status':
                $select[] = 'recycling_points.status as grouped_by';
                $query->groupBy('status');

                break;
            case 'fields':
                break;
        }
        $select[] = \DB::raw('COUNT("id") as total');

        $query->select($select)->with('issues', 'materials', 'fields', 'service', 'type');

        return $query;
    }

    public function getTableColumns(): array
    {
        $header = [];
        switch($this->groupedBy)
        {
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
                $header = MapPointToFieldModel::where('field_type_id', 1)->groupBy('value')->get()->pluck('value');
                break;
            case 'county':
                $header = CountyModel::all()->pluck('name');
                break;
            case 'status':
                $header = ['Necesita verificare', 'Verificat'];
                break;
            case 'fields':
                break;
        }
        $columns = [];
        if (\count($header))
        {
            foreach ($header as $head)
            {
                $columns[] = TextColumn::make($head)->label($head)->html();
            }
        }

        return $columns;
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
            ->paginated(false)
            ->columns($this->getTableColumns())
            ->view('filament.resources.reports-resource.pages.view', ['data'=>$this->data, 'header'=>$this->getTableColumns()]);
        // ->view('filament-tables::index');
    }
}
