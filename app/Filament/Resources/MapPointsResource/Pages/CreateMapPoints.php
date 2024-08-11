<?php

declare(strict_types=1);

namespace App\Filament\Resources\MapPointsResource\Pages;

use App\Filament\Resources\PointResource;
use App\Models\MapPointType as MapPointTypeModel;
use App\Models\PointType;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\CreateRecord;

class CreateMapPoints extends CreateRecord
{
    public ?array $data = [];

    public $lat;

    public $lon;

    public $city;

    protected static string $resource = PointResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()->id]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make(__('map_points.buttons.location_type'))
                        ->schema([
                            Select::make('service_type_id')
                                ->label(__('map_points.point_type_alt'))
                                ->relationship('serviceType', 'name')
                                ->required(),

                            TextInput::make('address'),
                            ViewField::make('map')
                                ->view('filament.forms.components.map'),
                        ]),
                    Wizard\Step::make(__('map_points.buttons.details'))
                        ->schema([
                            Select::make('point_type_id')
                                ->relationship('mapPointType', 'name')
                                ->options(fn (Get $get) => PointType::where('service_type_id', $get('service_type_id'))->pluck('name', 'id')->toArray())
                                ->required(),
                            Select::make('materials')
                                ->label(__('map_points.materials'))
                                ->relationship('materials', 'name')
                                ->preload()
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
                                    TimePicker::make('start_hour')->seconds(false)->hoursStep(1)
                                        ->minutesStep(10),
                                    TimePicker::make('end_hour')->seconds(false)->hoursStep(1)
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
                ])->columnSpanFull(),

            ]);
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
                ->relationship('recycleMaterials', 'name')
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
                        ->seconds(false)
                        ->hoursStep(1)
                        ->minutesStep(10)
                        ->readOnly(),
                    TimePicker::make('end_hour')
                        ->label(__('map_points.closing_time'))
                        ->seconds(false)
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
}
