<?php

declare(strict_types=1);

namespace App\Filament\Resources\PointResource\Actions\Table;

use App\Models\Point;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Collection;

class BulkUpdateInfo extends BulkAction
{
    public static function make(?string $name = null): static
    {
        return parent::make('bulkUpdateInfo');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('map_points.actions.bulk_update_info.title'));
        $this->color('warning');
        $this->icon('heroicon-o-pencil');
        $this->action(fn (Collection $records, array $data) => $this->updateInfo($records, $data));
    }

    public function getForm(Form $form): ?Form
    {
        return $form->schema([
            Select::make('field')
                ->label(__('map_points.actions.bulk_update_info.field'))
                ->options([
                    'notes' => __('map_points.fields.notes'),
                    'administered_by' => __('map_points.fields.administered_by'),
                    'business_name' => __('map_points.fields.business_name'),
                    'email' => __('map_points.fields.email'),
                    'phone' => __('map_points.fields.phone'),
                    'website' => __('map_points.fields.website'),
                    'observations' => __('map_points.fields.observations'),
                    'schedule' => __('map_points.fields.schedule'),
                ])
                ->required(),
            TextInput::make('field_value')
                ->label(__('map_points.actions.bulk_update_info.field_value'))
                ->required()
                ->maxLength(255),
        ])
            ->columns(2);
    }

    private function updateInfo(Collection $records, array $data)
    {
        $numberOfChangedPoints = 0;
        $records->each(function (Point $record) use ($data, &$numberOfChangedPoints) {
            $record->{$data['field']} = $data['field_value'];
            $record->save();
            $numberOfChangedPoints++;
        });

        if ($numberOfChangedPoints > 0) {
            Notification::make('success')
                ->success()
                ->title(__('map_points.actions.bulk_update_info.success', ['count' => $numberOfChangedPoints]))
                ->send();
        } else {
            Notification::make('error')
                ->title(__('map_points.actions.bulk_update_info.error'))
                ->danger()
                ->send();
        }
    }
}
