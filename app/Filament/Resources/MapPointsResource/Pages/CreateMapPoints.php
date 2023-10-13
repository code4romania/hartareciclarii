<?php

namespace App\Filament\Resources\MapPointsResource\Pages;

use App\Filament\Resources\MapPointsResource;
use App\Models\ActionLog as ActionLogModel;
use App\Models\MapPoint as MapPointModel;
use App\Models\MapPointService as MapPointServiceModel;
use App\Models\MapPointToField as MapPointToFieldModel;
use App\Models\MapPointType as MapPointTypeModel;
use App\Models\MaterialToMapPoint as MaterialToMapPointModel;
use App\Models\RecycleMaterial as RecycleMaterialModel;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class CreateMapPoints extends CreateRecord implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public $lat;

    public $lon;

    public $city;

    protected static string $resource = MapPointsResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record'=>$this->getRecord()->id]);
    }

    public function getData()
    {
        return $this->data;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Wizard::make([
                    Wizard\Step::make('Tip locatie')
                        ->schema([
                            Select::make('service')
                                ->label('Tip punct')
                                ->options(MapPointServiceModel::query()->pluck('display_name', 'id'))
                                ->required(),
                            TextInput::make('address'),
                            ViewField::make('map')
                                ->view('filament.forms.components.map'),
                        ]),
                    Wizard\Step::make('Detalii punct')
                        ->schema([
                            Select::make('type')
                                ->label('Tip punct')
                                ->options(function (callable $get)
                                {
                                    return MapPointTypeModel::query()->where('service_id', $get('service'))->pluck('display_name', 'id');
                                })
                                ->required(),
                            Select::make('materials')
                                ->label('Materiale')
                                ->options(RecycleMaterialModel::query()->pluck('name', 'id'))
                                ->multiple()
                                ->required(),
                            TextInput::make('managed_by')->required(),
                            TextInput::make('website'),
                            TextInput::make('email')->email(),
                            TextInput::make('phone_no'),
                            Repeater::make('opening_hours')
                                ->schema([
                                    Select::make('start_day')
                                        ->label('Start day')
                                        ->options([
                                            'mon' => __('common.week_days.mon'),
                                            'tue' => __('common.week_days.tue'),
                                            'wed' => __('common.week_days.wed'),
                                            'thu' => __('common.week_days.thu'),
                                            'fri' => __('common.week_days.fri'),
                                            'sat' => __('common.week_days.sat'),
                                            'sun' => __('common.week_days.sun'),
                                        ]),
                                    Select::make('end_day')
                                        ->label('End day')
                                        ->options([
                                            'mon' => __('common.week_days.mon'),
                                            'tue' => __('common.week_days.tue'),
                                            'wed' => __('common.week_days.wed'),
                                            'thu' => __('common.week_days.thu'),
                                            'fri' => __('common.week_days.fri'),
                                            'sat' => __('common.week_days.sat'),
                                            'sun' => __('common.week_days.sun'),
                                        ]),
                                    TimePicker::make('start_hour')->seconds('false')->hoursStep(1)
                                        ->minutesStep(10),
                                    TimePicker::make('end_hour')->seconds('false')->hoursStep(1)
                                        ->minutesStep(10),
                                ])
                                ->columns(4),
                            Textarea::make('notes'),
                            Checkbox::make('offers_transport'),
                            Checkbox::make('offers_money'),
                            FileUpload::make('image')
                                // ->image()
                                // ->imageEditor()
                                ->multiple(),
                        ]),
                    Wizard\Step::make('Confirma informatiile')
                        ->schema(function ($get)
                        {
                            return self::getContentPreviewDescription($get);
                        }),
                ])
                    ->submitAction(new HtmlString(Blade::render(<<<'BLADE'
						    <x-filament::button
						        type="submit"
						        size="sm"
						    >
						        Submit
						    </x-filament::button>
						BLADE))),
            ])->statePath('data');
    }

    public static function getContentPreviewDescription($get)
    {
        // return [];

        return [
            TextInput::make('type_test')->label('Type')->readOnly()->placeholder($get('type') ? MapPointTypeModel::find($get('type'))->display_name : $get('type')),
            Select::make('materials')
                ->label('Materiale')
                ->options(RecycleMaterialModel::query()->pluck('name', 'id'))
                ->default($get('materials'))
                ->multiple(),
            TextInput::make('address')->label('Address')->readOnly()->default($get('address')),
            TextInput::make('managed_by')->label('Managed by')->readOnly()->default($get('managed_by')),
            TextInput::make('website')->label('Website')->readOnly()->default($get('website')),
            TextInput::make('email')->label('Email')->readOnly()->default($get('email')),
            TextInput::make('phone_no')->label('Phone no')->readOnly()->default($get('phone_no')),
            Repeater::make('opening_hours')
                ->schema([
                    Select::make('start_day')
                        ->label('Start day')
                        ->options([
                            'mon' => __('common.week_days.mon'),
                            'tue' => __('common.week_days.tue'),
                            'wed' => __('common.week_days.wed'),
                            'thu' => __('common.week_days.thu'),
                            'fri' => __('common.week_days.fri'),
                            'sat' => __('common.week_days.sat'),
                            'sun' => __('common.week_days.sun'),
                        ]),
                    Select::make('end_day')
                        ->label('End day')
                        ->options([
                            'mon' => __('common.week_days.mon'),
                            'tue' => __('common.week_days.tue'),
                            'wed' => __('common.week_days.wed'),
                            'thu' => __('common.week_days.thu'),
                            'fri' => __('common.week_days.fri'),
                            'sat' => __('common.week_days.sat'),
                            'sun' => __('common.week_days.sun'),
                        ]),
                    // >pluck('id')->toArray())
                    TimePicker::make('start_hour')->label('Opening time')->seconds('false')->hoursStep(1)
                        ->minutesStep(10)->readOnly(),
                    TimePicker::make('end_hour')->label('Closing time')->seconds('false')->hoursStep(1)
                        ->minutesStep(10)->readOnly(),
                ])->default($get('opening_hours'))
                ->columns(4),
            Textarea::make('notes')->label('Notes')->readOnly()->default($get('notes')),
            Checkbox::make('offers_transport')->label('Offers transport')->default($get('notes')),
            Checkbox::make('offers_money')->label('Offers money')->default($get('notes')),

        ];
    }

    public function getTitle(): string
    {
        return 'Sugereaza un nou punct pe harta';
    }

    protected function getFormActions(): array
    {
        return [
            //  Actions\CreateAction::make(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $data['lat'] = $this->lat;
        $data['lon'] = $this->lon;

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = new MapPointModel();
        $record->service_id = data_get($data, 'service');
        $record->point_type_id = data_get($data, 'type');
        $record->lat = data_get($data, 'lat');
        $record->lon = data_get($data, 'lon');
        $record->location = \DB::raw('GeomFromText("POINT(' . data_get($data, 'lon') . ' ' . data_get($data, 'lat') . ')")');
        $record->created_by = data_get($data, 'created_by');
        $record->point_source = 'user';
        $record->status = 0;

        $judet = \DB::select(\DB::raw('SELECT * FROM judete_geo jg WHERE ST_CONTAINS(jg.pol, Point(' . data_get($data, 'lon') . ', ' . data_get($data, 'lat') . '))')->getValue(\DB::connection()->getQueryGrammar()));
        $record->save();
        if (!empty($judet))
        {
            $field = collect([
                'field_type_id' => 2,
                'recycling_point_id' => $record->id,
                'value' => $judet[0]->name,
            ]);
            MapPointToFieldModel::addValueToPoint($field);
        }

        $fields = [
            collect([
                'field_type_id' => 1,
                'recycling_point_id' => $record->id,
                'value' => $this->city,
            ]),
            collect([
                'field_type_id' => 4,
                'recycling_point_id' => $record->id,
                'value' => data_get($data, 'address'),
            ]),
            collect([
                'field_type_id' => 3,
                'recycling_point_id' => $record->id,
                'value' => data_get($data, 'managed_by'),
            ]),
            collect([
                'field_type_id' => 9,
                'recycling_point_id' => $record->id,
                'value' => data_get($data, 'website'),
            ]),
            collect([
                'field_type_id' => 8,
                'recycling_point_id' => $record->id,
                'value' => data_get($data, 'email'),
            ]),
            collect([
                'field_type_id' => 7,
                'recycling_point_id' => $record->id,
                'value' => json_encode(data_get($data, 'opening_hours', [])),
            ]),
            collect([
                'field_type_id' => 10,
                'recycling_point_id' => $record->id,
                'value' => data_get($data, 'notes'),
            ]),
            collect([
                'field_type_id' => 6,
                'recycling_point_id' => $record->id,
                'value' => (int) data_get($data, 'offers_transport', 0),
            ]),
            collect([
                'field_type_id' => 5,
                'recycling_point_id' => $record->id,
                'value' => (int) data_get($data, 'offers_money', 0),
            ]),
            collect([
                'field_type_id' => 12,
                'recycling_point_id' => $record->id,
                'value' => data_get($data, 'phone_no'),
            ]),
        ];
        MapPointToFieldModel::addValuesToPoint($fields);

        if (!empty(data_get($data, 'materials')))
        {
            foreach (data_get($data, 'materials') as $material_id)
            {
                $item = MaterialToMapPointModel::firstOrCreate(['material_id'=>$material_id, 'recycling_point_id'=>$record->id]);
            }
        }
        $action = collect([
            'model' => MapPointModel::class,
            'model_id'=> $record->id,
            'user_id' => auth()->user()->id,
            'action' => 'created',
            'old_values' => [],
            'new_values' => [],
        ]);

        ActionLogModel::logAction($action);

        return $record;
    }

    public function getFormStatePath(): ?string
    {
        return 'data';
    }
}
