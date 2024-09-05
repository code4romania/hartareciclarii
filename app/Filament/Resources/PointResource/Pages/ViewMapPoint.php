<?php

declare(strict_types=1);

namespace App\Filament\Resources\PointResource\Pages;

use App\Enums\Point\Status;
use App\Filament\Forms\Components\LeafletAutocomplete;
use App\Filament\Resources\PointResource;
use App\Models\ActionLog as ActionLogModel;
use App\Models\PointType;
use Dotswan\MapPicker\Infolists\MapEntry;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ViewMapPoint extends ViewRecord
{
    protected static string $resource = PointResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('changeStatus')
                ->label(__('map_points.buttons.change_status'))
                ->icon('heroicon-m-arrows-right-left')
                ->form([
                    Select::make('status')
                        ->label(__('map_points.fields.status'))
                        ->options(Status::options())
                        ->default($this->record->status->value)
                        ->required(),
                ])
                ->modal()
                ->action(function (array $data): void {
                    $status = Status::tryFrom($data['status']);
                    $this->record->changeStatus($status);
                    Notification::make()
                        ->title(__('map_points.point_save_success'))
                        ->success()
                        ->send();
                }),
            Action::make('updateGroup')
                ->label(__('map_points.buttons.set_group'))
                ->form([
                    Select::make('group_id')
                        ->label(__('map_points.fields.group'))
                        ->relationship('pointGroup', 'name')
                        ->default($this->record->point_group_id)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $this->record->changeGroup((int) $data['group_id']);
                    Notification::make()
                        ->title(__('map_points.point_save_success'))
                        ->success()
                        ->send();
                })
                ->icon('heroicon-o-user-group')
                ->color('info'),
            Action::make('delete')
                ->requiresConfirmation()
                ->action(function () {
                    $this->getRecord()->delete();

                    return redirect($this->getResource()::getUrl('index'));
                })
                ->color('danger'),

        ];
    }

    public function getTitle(): string | Htmlable
    {
        $record = $this->getRecord();
        $title = '#' . $this->getRecord()->id . '<fi-badge>' . $record->status->label() . '</fi-badge>';

        return new HtmlString($title);
    }

    public function getSubHeading(): string | Htmlable
    {
        return __(
            'map_points.subheading',
            [
                'serviceType' => $this->getRecord()->serviceType->name,
                'pointType' => $this->getRecord()->pointType->name,
                'administeredBy' => $this->getRecord()->administered_by,
                'group' => $this->getRecord()->pointGroup?->name,
            ]
        );
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema(
            [
                Section::make(__('map_points.sections.location'))
                    ->schema(
                        [
                            TextEntry::make('address')
                                ->icon('heroicon-s-map-pin')
                                ->label(__('map_points.fields.address')),

                            TextEntry::make('location')
                                ->icon('heroicon-s-ellipsis-horizontal-circle')
                                ->label(__('map_points.fields.coordinate')),

                            TextEntry::make('notes')
                                ->icon('heroicon-s-pencil-square')
                                ->label(__('map_points.fields.notes')),
                        ]
                    )
                    ->headerActions(
                        [
                            InfolistAction::make('updateLocation')
                                ->hiddenLabel()
                                ->icon('heroicon-s-pencil-square')
                                ->fillForm($this->record->toArray())
                                ->form(
                                    [
                                        LeafletAutocomplete::make('location')
                                            ->hiddenLabel(),
                                        Textarea::make('notes')
                                            ->label(__('map_points.fields.notes')),

                                    ]
                                )->action(function (array $data, $livewire) {
                                    $this->record->update([
                                        'address' => $data['address'],
                                        'notes' => $data['notes'],
                                        'city_id' => $data['city_id'],
                                        'county_id' => $data['county_id'],

                                    ]);

                                    $this->record->location->latitude = (float) $data['location']['lat'];
                                    $this->record->location->longitude = (float) $data['location']['lng'];
                                    $this->record->save();
                                    $livewire->dispatch('refreshMap');
                                    Notification::make()
                                        ->title(__('map_points.point_save_success'))
                                        ->success()
                                        ->send();
                                    $this->refreshFormData([
                                        'address', 'notes', 'location',
                                    ]);
                                }),
                        ]
                    )->columnSpan(3),

                MapEntry::make('location')
                    ->label(__('map_points.sections.map'))
                    ->state(fn ($record) => ['lat' => $record?->location->latitude, 'lng' => $record?->location->longitude])
                    ->draggable(false)
                    ->zoom(18)
                    ->columnSpan(9),

                Section::make(__('map_points.sections.details'))
                    ->schema([
                        TextEntry::make('pointType.name')
                            ->label(__('map_points.fields.point_type')),

                        TextEntry::make('materials.name')
                            ->label(__('map_points.fields.materials')),

                        TextEntry::make('business_name')
                            ->hidden($this->showField('can_have_business_name'))
                            ->label(__('map_points.fields.business_name')),


                        TextEntry::make('administered_by')
                            ->label(__('map_points.fields.administered_by')),

                        TextEntry::make('website')
                            ->hidden($this->showField('can_have_business_name'))
                            ->label(__('map_points.fields.website')),

                        TextEntry::make('email')
                            ->hidden($this->showField('can_have_business_name'))
                            ->label(__('map_points.fields.email')),

                        TextEntry::make('phone')
                            ->hidden($this->showField('can_have_business_name'))
                            ->label(__('map_points.fields.phone')),

                        TextEntry::make('schedule')
                            ->columns(1)
                            ->label(__('map_points.fields.schedule')),

                        TextEntry::make('observations')
                            ->label(__('map_points.fields.observations')),

                        IconEntry::make('offers_transport')
                            ->icon('heroicon-s-truck')
                            ->boolean()
                            ->hidden($this->showField('can_offer_transport'))
                            ->inlineLabel()
                            ->label(__('map_points.fields.offers_transport')),

                        IconEntry::make('offers_money')
                            ->icon('heroicon-s-banknotes')
                            ->boolean()
                            ->hidden($this->showField('can_offer_money'))
                            ->inlineLabel()
                            ->label(__('map_points.fields.offers_money')),

                        IconEntry::make('offers_vouchers')
                            ->icon('heroicon-s-ticket')
                            ->boolean()
                            ->hidden($this->showField('can_offer_vouchers'))
                            ->inlineLabel()
                            ->label(__('map_points.fields.offers_vouchers')),

                        IconEntry::make('free_of_charge')
                            ->boolean()
                            ->hidden($this->showField('can_request_payment'))
                            ->inlineLabel()
                            ->label(__('map_points.fields.free_of_charge')),

                    ])
                    ->columns(3)
                    ->columnSpan(12)
                    ->headerActions([
                        InfolistAction::make('editDetails')
                            ->fillForm($this->record->toArray())
                            ->form(
                                [
                                    Select::make('point_type_id')
                                        ->label(__('map_points.fields.point_type'))
                                        ->relationship('pointType', 'name')
                                        ->options(PointType::where('service_type_id', $this->record->service_type_id)->get()->pluck('name', 'id')->toArray())
                                        ->required(),
                                    Select::make('materials')
                                        ->label(__('map_points.fields.materials'))
                                        ->relationship('materials', 'name')
                                        ->multiple()
                                        ->required(),
                                    TextInput::make('business_name')
                                        ->label(__('map_points.fields.business_name'))
                                        ->hidden(fn () => $this->showField('can_have_business_name')),

                                    TextInput::make('administered_by')
                                        ->label(__('map_points.fields.administered_by'))
                                        ->disabled(fn (Get $get) => $get('unknown_administered_by'))
                                        ->required(fn (Get $get) => $get('unknown_administered_by') === false),
                                    Checkbox::make('unknown_administered_by')
                                        ->live()
                                        ->afterStateUpdated(function (Set $set){
                                            $set('administered_by', null);
                                        })
                                        ->label(__('add_point.details.unknown_program'))
                                        ->default(false),

                                    TextInput::make('schedule')
                                        ->label(__('map_points.fields.schedule'))
                                        ->disabled(fn (Get $get) => $get('unknown_schedule'))
                                        ->required(fn (Get $get) => $get('unknown_schedule') === false),
                                    Checkbox::make('unknown_schedule')
                                        ->live()
                                        ->afterStateUpdated(function (Set $set){
                                            $set('schedule', null);
                                        })
                                        ->label(__('add_point.details.unknown_program'))
                                        ->default(false),

                                    TextInput::make('website')
                                        ->url()
                                        ->label(__('map_points.fields.website'))
                                        ->hidden(fn () => $this->showField( 'can_have_business_name')),

                                    TextInput::make('email')
                                        ->label(__('map_points.fields.email'))
                                        ->email()
                                        ->hidden(fn () => $this->showField( 'can_have_business_name')),

                                    TextInput::make('phone')
                                        ->label(__('map_points.fields.phone'))
                                        ->hidden(fn () => $this->showField( 'can_have_business_name')),

                                    Textarea::make('observations')
                                        ->label(__('map_points.fields.observations')),
                                    Group::make(
                                        [
                                            Toggle::make('offers_transport')
                                                ->hidden($this->showField('can_offer_transport'))
                                                ->label(__('map_points.fields.offers_transport')),
                                            Toggle::make('offers_money')
                                                ->hidden($this->showField('can_offer_money'))
                                                ->label(__('map_points.fields.offers_money')),
                                            Toggle::make('offers_vouchers')
                                                ->hidden($this->showField('can_offer_vouchers'))
                                                ->label(__('map_points.fields.offers_vouchers')),
                                            Toggle::make('free_of_charge')
                                                ->hidden($this->showField('can_request_payment'))
                                                ->label(__('map_points.fields.free_of_charge')),
                                        ]
                                    )->columns(4),

                                ]
                            )->label(__('map_points.buttons.edit_details'))
                            ->action(fn (array $data) => $this->record->update($data)),
                    ]),

            ]
        )->columns(12);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ActionLogModel::whereModel(\get_class($this->getRecord()))->whereModelId($this->getRecord()->id)->orderBy('created_at', 'desc'))
            ->columns([
                TextColumn::make('user.name')
                    ->formatStateUsing(function (string $state, $record) {
                    })
                    ->html(),
                TextColumn::make('action')
                    ->formatStateUsing(function (string $state, $record) {
                        return trans('actions.' . $record->action);
                    })
                    ->html(),
                TextColumn::make('old_values')
                    ->formatStateUsing(function (string $state, $record) {
                        return ActionLogModel::formatValuesText($record, 'old_values');
                    })
                    ->wrap()
                    ->html(),
                TextColumn::make('new_values')
                    ->formatStateUsing(function (string $state, $record) {
                        return ActionLogModel::formatValuesText($record, 'new_values');
                    })
                    ->wrap()
                    ->html(),
                TextColumn::make('created_at'),

            ]);
    }

    private function showField(string $string): bool
    {
        if ($this->record->serviceType === null) {
            return false;
        }

        return  !data_get($this->record->serviceType->toArray(), $string, false);
    }
}
