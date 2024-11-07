<?php

declare(strict_types=1);

namespace App\Filament\Imports;

use App\Enums\Point\Source;
use App\Models\City;
use App\Models\Material;
use App\Models\Point;
use App\Models\PointGroup;
use App\Models\ServiceType;
use Carbon\CarbonInterface;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use MatanYadaev\EloquentSpatial\Objects\Point as PointObject;

class PointImporter extends Importer
{
    protected static ?string $model = Point::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('latitude')
                ->label(__('map_points.fields.latitude'))
                ->example('27.9506')
                ->guess(['latitude', 'lat', 'latitudine'])
                ->validationAttribute('latitude')
                ->requiredMapping(),

            ImportColumn::make('longitude')
                ->label(__('map_points.fields.longitude'))
                ->example('27.9506')
                ->guess(['longitude', 'long', 'longitudine'])
                ->validationAttribute('longitude')
                ->requiredMapping(),

            ImportColumn::make('pointType')
                ->label(__('map_points.fields.point_type'))
                ->example('Punct de colectare')
                ->validationAttribute('pointType')
                ->requiredMapping()
                ->relationship('pointType', 'name')
                ->rules(
                    [
                        'required',
                        'string',
                    ]
                ),

            ImportColumn::make('county')
                ->requiredMapping()
                ->example('București')
                ->label(__('map_points.county'))
                ->validationAttribute('county')
                ->fillRecordUsing(function (Point $record, string $state, Get $get) {
                    $city = City::search()
                        ->where('county', $state)
                        ->first();
                    if (! $city) {
                        throw ValidationException::withMessages(
                            [
                                'county' => __('validation.county'),
                            ]
                        );
                    }
                    $record->county_id = $city->county_id;
                })
                ->rules(
                    [
                        'required',
                        'string',
                    ]
                ),

            ImportColumn::make('city')
                ->label(__('map_points.city'))
                ->example('Sector 2')
                ->validationAttribute('city')
                ->fillRecordUsing(function (Point $record, string $state) {
                    $cityId = City::search($state)->where('county', $record->county->name)->first()?->id;
                    if (! $cityId) {
                        throw ValidationException::withMessages(
                            [
                                'city' => __('validation.exists', [
                                    'attribute' => __('map_points.city'),
                                ]),
                            ]
                        );
                    }
                    $record->city_id = $cityId;
                })
                ->requiredMapping()
                ->rules(
                    [
                        'required',
                        'string',
                    ]
                ),

            ImportColumn::make('address')
                ->requiredMapping()
                ->validationAttribute('address')
                ->label(__('map_points.fields.address'))
                ->example('Strada Ștefan cel Mare 1')
                ->rules(
                    [
                        'required',
                        'string',
                    ]
                )
                ->ignoreBlankState(),

            ImportColumn::make('notes')
                ->label(__('map_points.fields.notes'))
                ->ignoreBlankState(),

            ImportColumn::make('observations')
                ->label(__('map_points.fields.observations'))
                ->ignoreBlankState(),

            ImportColumn::make('administered_by')
                ->label(__('map_points.fields.administered_by'))
                ->ignoreBlankState(),

            ImportColumn::make('business_name')
                ->label(__('map_points.fields.business_name'))
                ->ignoreBlankState(),

            ImportColumn::make('offers_money')
                ->label(__('map_points.fields.offers_money'))
                ->ignoreBlankState(),

            ImportColumn::make('offers_transport')
                ->label(__('map_points.fields.offers_transport'))
                ->ignoreBlankState(),

            ImportColumn::make('schedule')
                ->label(__('map_points.fields.schedule'))
                ->ignoreBlankState(),

            ImportColumn::make('email')
                ->label(__('map_points.fields.email'))
                ->ignoreBlankState(),

            ImportColumn::make('website')
                ->label(__('map_points.fields.website'))
                ->ignoreBlankState(),

            ImportColumn::make('materials')
                ->label(__('map_points.fields.materials'))
                ->example('DEEE mici;DEEE mari;Telefoane mobile;Baterii;Becuri, neoane, LED')
                ->array(';')
                ->ignoreBlankState(),

        ];
    }

    public function getJobRetryUntil(): ?CarbonInterface
    {
        return null;
    }

    public function resolveRecord(): ?Point
    {
        Validator::make(
            ['latitude' => $this->data['latitude'], 'longitude' => $this->data['longitude']],
            [
                'latitude' => ['required', 'regex:^\d{2}\.\d{5}\d*^'],
                'longitude' => ['required', 'regex:^\d{2}\.\d{5}\d*^'],
            ],
            [
                'latitude.regex' => __('validation.regex_valid_latitude'),
                'longitude.regex' => __('validation.regex_valid_longitude'),
            ]
        )->validate();
        $this->data['location'] = new PointObject((float) $this->data['latitude'], (float) $this->data['longitude']);
        unset($this->data['latitude'], $this->data['longitude']);

        return new Point();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your point import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            Select::make('service_type_id')
                ->label(__('map_points.fields.service_type'))
                ->live()
                ->options(
                    ServiceType::all()->pluck('name', 'id')->toArray()
                )->required(),

            Toggle::make('import_materials')
                ->label(__('map_points.import_materials'))
                ->hidden(fn (Get $get) => ! ServiceType::find($get('service_type_id'))?->can_collect_materials)
                ->default(false),

            Section::make(__('map_points.use_default_values'))->columns(2)->schema(
                [
                    TextInput::make('default_administered_by')
                        ->label(__('map_points.fields.administered_by')),
                    TextInput::make('default_business_name')
                        ->label(__('map_points.fields.business_name')),

                    Toggle::make('default_offers_money')
                        ->label(__('map_points.fields.offers_money')),
                    Toggle::make('default_offers_transport')
                        ->label(__('map_points.fields.offers_transport')),

                    TextInput::make('default_schedule')
                        ->label(__('map_points.fields.schedule')),

                    Select::make('point_group_id')
                        ->label(__('map_points.fields.group'))
                        ->options(
                            PointGroup::all()->pluck('name', 'id')->toArray()
                        ),

                ]
            ),

        ];
    }

    public function saveRecord(): void
    {
        $materials = $this->data['materials'] ?? [];
        unset($this->data['materials']);
        unset($this->record->materials);

        $this->setMetadata();

        $this->checkDefaultFields();

        parent::saveRecord();

        if (! empty($this->options['import_materials'])) {
            collect($materials)->each(function ($material) {
                $this->record->materials()->attach(Material::search($material)->first());
            });
        }
    }

    private function checkDefaultFields(): void
    {
        if (blank($this->record->administered_by)) {
            $this->record->administered_by = $this->options['default_administered_by'];
        }
        if (blank($this->record->business_name)) {
            $this->record->business_name = $this->options['default_business_name'];
        }
        if (blank($this->record->offers_money)) {
            $this->record->offers_money = $this->options['default_offers_money'];
        }
        if (blank($this->record->offers_transport)) {
            $this->record->offers_transport = $this->options['default_offers_transport'];
        }
        if (blank($this->record->schedule)) {
            $this->record->schedule = $this->options['default_schedule'];
        }
    }

    private function setMetadata(): void
    {
        $this->record->location = $this->data['location'];
        $this->record->service_type_id = $this->options['service_type_id'];
        $this->record->created_by = auth()->id();
        $this->record->source = Source::IMPORT;
        $this->record->import_id = $this->import->id;
    }

    public function afterValidate(): void
    {
        if (blank($this->import->service_type_id)) {
            $this->import->service_type_id = $this->options['service_type_id'];
            $this->import->save();
        }
    }

    public function getValidationAttributes(): array
    {
        return [
            'latitude' => __('map_points.fields.latitude'),
            'longitude' => __('map_points.fields.longitude'),
            'pointType' => __('map_points.fields.point_type'),
            'county' => __('map_points.county'),
            'city' => __('map_points.city'),
            'address' => __('map_points.fields.address'),
            'notes' => __('map_points.fields.notes'),
            'observations' => __('map_points.fields.observations'),
            'administered_by' => __('map_points.fields.administered_by'),
            'business_name' => __('map_points.fields.business_name'),
            'offers_money' => __('map_points.fields.offers_money'),
            'offers_transport' => __('map_points.fields.offers_transport'),
            'schedule' => __('map_points.fields.schedule'),
            'email' => __('map_points.fields.email'),
            'website' => __('map_points.fields.website'),
            'materials' => __('map_points.fields.materials'),

        ];
    }
}
