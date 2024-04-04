<?php

declare(strict_types=1);

namespace App\Filament\Resources\MapPointsResource\Pages;

use App\Enums\MapPointTypes;
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
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()->id]);
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
                    Wizard\Step::make(__('map_points.buttons.location_type'))
                        ->schema([
                            Select::make('service')
                                ->label(__('map_points.point_type_alt'))
                                ->options(MapPointServiceModel::query()->pluck('display_name', 'id'))
                                ->required(),
                            TextInput::make('address'),
                            ViewField::make('map')
                                ->view('filament.forms.components.map'),
                        ]),
                    Wizard\Step::make(__('map_points.buttons.details'))
                        ->schema([
                            Select::make('type')
                                ->label(__('map_points.point_type_alt'))
                                ->options(function (callable $get) {
                                    return MapPointTypeModel::query()->where('service_id', $get('service'))->pluck('display_name', 'id');
                                })
                                ->required(),
                            Select::make('materials')
                                ->label(__('map_points.materials'))
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
                                        ->label(__('map_points.start_day'))
                                        ->options(__('common.week_days')),
                                    Select::make('end_day')
                                        ->label(__('map_points.end_day'))
                                        ->options(__('common.week_days')),
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
                    Wizard\Step::make(__('map_points.buttons.confirm_info'))
                        ->schema(function ($get) {
                            return self::getContentPreviewDescription($get);
                        }),
                ])
                    ->submitAction(new HtmlString(Blade::render(
                        <<<'BLADE'
						    <x-filament::button
						        type="submit"
						        size="sm"
						    >
						        Submit
						    </x-filament::button>
						BLADE
                    ))),
            ])->statePath('data');
    }

    public static function getContentPreviewDescription($get)
    {
        // return [];

        return [
            TextInput::make('type_test')
                ->label(__('map_points.point_type'))
                ->readOnly()
                ->placeholder($get('type') ? MapPointTypeModel::find($get('type'))->display_name : $get('type')),
            Select::make('materials')
                ->label(__('map_points.materials'))
                ->options(RecycleMaterialModel::query()->pluck('name', 'id'))
                ->default($get('materials'))
                ->multiple(),
            TextInput::make('address')
                ->label(__('map_points.address'))
                ->readOnly()
                ->default($get('address')),
            TextInput::make('managed_by')
                ->label(__('map_points.managed_by'))
                ->readOnly()
                ->default($get('managed_by')),
            TextInput::make('website')
                ->label(__('map_points.website'))
                ->readOnly()
                ->default($get('website')),
            TextInput::make('email')
                ->label(__('map_points.email'))
                ->readOnly()
                ->default($get('email')),
            TextInput::make('phone_no')
                ->label(__('map_points.phone_no'))
                ->readOnly()
                ->default($get('phone_no')),
            Repeater::make('opening_hours')
                ->schema([
                    Select::make('start_day')
                        ->label(__('map_points.start_day'))
                        ->options(__('common.week_days')),
                    Select::make('end_day')
                        ->label(__('map_points.end_day'))
                        ->options(__('common.week_days')),
                    // >pluck('id')->toArray())
                    TimePicker::make('start_hour')
                        ->label(__('map_points.opening_time'))
                        ->seconds('false')
                        ->hoursStep(1)
                        ->minutesStep(10)
                        ->readOnly(),
                    TimePicker::make('end_hour')
                        ->label(__('map_points.closing_time'))
                        ->seconds('false')
                        ->hoursStep(1)
                        ->minutesStep(10)
                        ->readOnly(),
                ])
                ->default($get('opening_hours'))
                ->columns(4),
            Textarea::make('notes')
                ->label(__('map_points.notes'))
                ->readOnly()
                ->default($get('notes')),
            Checkbox::make('offers_transport')
                ->label(__('map_points.offers_transport'))
                ->default($get('notes')),
            Checkbox::make('offers_money')
                ->label(__('map_points.offers_money'))
                ->default($get('notes')),

        ];
    }

    public function getTitle(): string
    {
        return __('map_points.suggest_new_point');
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
        $record->location = \DB::raw('ST_GeomFromText("POINT(' . data_get($data, 'lon') . ' ' . data_get($data, 'lat') . ')")');
        $record->created_by = data_get($data, 'created_by');
        $record->point_source = 'user';
        $record->status = 0;

        $judet = \DB::select(\DB::raw('SELECT * FROM judete_geo jg WHERE ST_CONTAINS(jg.pol, Point(' . data_get($data, 'lon') . ', ' . data_get($data, 'lat') . '))')->getValue(\DB::connection()->getQueryGrammar()));
        $record->save();
        if (! empty($judet)) {
            $field = collect([
                'field_type_id' => MapPointTypes::County,
                'recycling_point_id' => $record->id,
                'value' => $judet[0]->name,
            ]);
            MapPointToFieldModel::addValueToPoint($field);
        }

        $fields = [
            collect([
                'field_type_id' => MapPointTypes::City,
                'recycling_point_id' => $record->id,
                'value' => $this->city,
            ]),
            collect([
                'field_type_id' => MapPointTypes::Address,
                'recycling_point_id' => $record->id,
                'value' => data_get($data, 'address'),
            ]),
            collect([
                'field_type_id' => MapPointTypes::ManagedBy,
                'recycling_point_id' => $record->id,
                'value' => data_get($data, 'managed_by'),
            ]),
            collect([
                'field_type_id' => MapPointTypes::Website,
                'recycling_point_id' => $record->id,
                'value' => data_get($data, 'website'),
            ]),
            collect([
                'field_type_id' => MapPointTypes::Email,
                'recycling_point_id' => $record->id,
                'value' => data_get($data, 'email'),
            ]),
            collect([
                'field_type_id' => MapPointTypes::OpeningHours,
                'recycling_point_id' => $record->id,
                'value' => json_encode(data_get($data, 'opening_hours', [])),
            ]),
            collect([
                'field_type_id' => MapPointTypes::Notes,
                'recycling_point_id' => $record->id,
                'value' => data_get($data, 'notes'),
            ]),
            collect([
                'field_type_id' => MapPointTypes::OffersTransport,
                'recycling_point_id' => $record->id,
                'value' => (int) data_get($data, 'offers_transport', 0),
            ]),
            collect([
                'field_type_id' => MapPointTypes::OffersMoney,
                'recycling_point_id' => $record->id,
                'value' => (int) data_get($data, 'offers_money', 0),
            ]),
            collect([
                'field_type_id' => MapPointTypes::PhoneNo,
                'recycling_point_id' => $record->id,
                'value' => data_get($data, 'phone_no'),
            ]),
        ];
        MapPointToFieldModel::addValuesToPoint($fields);

        if (! empty(data_get($data, 'materials'))) {
            foreach (data_get($data, 'materials') as $material_id) {
                $item = MaterialToMapPointModel::firstOrCreate(['material_id' => $material_id, 'recycling_point_id' => $record->id]);
            }
        }
        $action = collect([
            'model' => MapPointModel::class,
            'model_id' => $record->id,
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
