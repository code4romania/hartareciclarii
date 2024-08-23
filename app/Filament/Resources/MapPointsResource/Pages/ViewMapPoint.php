<?php

declare(strict_types=1);

namespace App\Filament\Resources\MapPointsResource\Pages;

use App\Enums\DaysEnum;
use App\Enums\Point\Status;
use App\Filament\Resources\PointResource;
use App\Models\ActionLog as ActionLogModel;
use Filament\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
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
                    $this->record->changeGroup($data['group_id']);
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
                                        TextInput::make('address')
                                            ->label(__('map_points.fields.address'))
                                            ->required(),
                                        \Filament\Forms\Components\Section::make(__('map_points.sections.location'))
                                            ->schema(
                                                [
                                                    TextInput::make('latitude')
                                                        ->label(__('map_points.fields.latitude'))
                                                        ->formatStateUsing(function () {
                                                            return $this->record->location->latitude;
                                                        })
                                                        ->required(),
                                                    TextInput::make('longitude')
                                                        ->label(__('map_points.fields.longitude'))
                                                        ->formatStateUsing(function () {
                                                            return $this->record->location->longitude;
                                                        })
                                                        ->required(),
                                                ]
                                            )->columns(2)
                                            ->compact(),
                                        Textarea::make('notes')
                                            ->label(__('map_points.fields.notes')),

                                    ]
                                )->action(function (array $data) {
                                    $this->record->update([
                                        'address' => $data['address'],
                                        'notes' => $data['notes'],
                                    ]);
                                    $this->record->location->latitude = (float) $data['latitude'];
                                    $this->record->location->longitude = (float) $data['longitude'];
                                    $this->record->save();
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

                ViewEntry::make('map_location')
                    ->columnSpan(9)
                    ->view('filament.infolist.map'),

                Section::make(__('map_points.sections.details'))
                    ->schema([
                        TextEntry::make('pointType.name')
                            ->label(__('map_points.fields.point_type')),
                        TextEntry::make('materials.name')
                            ->label(__('map_points.fields.materials')),
                        TextEntry::make('administered_by')
                            ->label(__('map_points.fields.administered_by')),
                        TextEntry::make('website')
                            ->label(__('map_points.fields.website')),
                        TextEntry::make('email')
                            ->label(__('map_points.fields.email')),
                        TextEntry::make('phone')
                            ->label(__('map_points.fields.phone')),
                        Section::make(__('map_points.fields.schedule'))
                            ->collapsed()
                            ->schema([
                                RepeatableEntry::make('schedule')
                                    ->hiddenLabel()
                                    ->schema([
                                        TextEntry::make('day')
                                            ->hiddenLabel()
                                            ->inlineLabel()
                                            ->label(__('map_points.fields.day')),
                                        TextEntry::make('start')
                                            ->inlineLabel()
                                            ->hiddenLabel()
                                            ->label(__('map_points.fields.opening_time')),
                                        TextEntry::make('end')
                                            ->inlineLabel()
                                            ->hiddenLabel()
                                            ->label(__('map_points.fields.closing_time')),
                                    ])
                                    ->columns(3),
                            ])
                            ->columns(1)
                            ->label(__('map_points.fields.schedule')),
                        TextEntry::make('observations')
                            ->label(__('map_points.fields.observations')),
                        IconEntry::make('offers_transport')
                            ->icon('heroicon-s-truck')
                            ->boolean()
                            ->inlineLabel()
                            ->label(__('map_points.fields.offers_transport')),
                        IconEntry::make('offers_money')
                            ->icon('heroicon-s-banknotes')
                            ->boolean()
                            ->inlineLabel()
                            ->label(__('map_points.fields.offers_money')),
                        IconEntry::make('offers_vouchers')
                            ->icon('heroicon-s-ticket')
                            ->boolean()
                            ->inlineLabel()
                            ->label(__('map_points.fields.offers_vouchers')),
                        IconEntry::make('free_of_charge')
                            ->boolean()
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
                                        ->required(),
                                    Select::make('materials')
                                        ->label(__('map_points.fields.materials'))
                                        ->relationship('materials', 'name')
                                        ->multiple()
                                        ->required(),
                                    TextInput::make('administered_by')
                                        ->label(__('map_points.fields.administered_by'))
                                        ->required(),
                                    TextInput::make('website')
                                        ->label(__('map_points.fields.website')),
                                    TextInput::make('email')
                                        ->label(__('map_points.fields.email')),
                                    TextInput::make('phone')
                                        ->label(__('map_points.fields.phone')),
                                    TextInput::make('schedule')
                                        ->label(__('map_points.fields.schedule'))
                                        ->required(),
                                    Textarea::make('observations')
                                        ->label(__('map_points.fields.observations')),
                                    Group::make(
                                        [
                                            Toggle::make('offers_transport')
                                                ->label(__('map_points.fields.offers_transport')),
                                            Toggle::make('offers_money')
                                                ->label(__('map_points.fields.offers_money')),
                                            Toggle::make('offers_vouchers')
                                                ->label(__('map_points.fields.offers_vouchers')),
                                            Toggle::make('free_of_charge')
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
}
