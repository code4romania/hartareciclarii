<?php

declare(strict_types=1);

namespace App\Filament\Resources\PointResource\Actions\Table;

use App\Filament\Resources\PointResource\Actions\Page\AddPoint;
use App\Models\Point;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Collection;

class BulkAddMaterials extends BulkAction
{
    public static function make(?string $name = null): static
    {
        return parent::make('bulkAddMaterials');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('map_points.actions.bulk_add_materials.title'));

        $this->color('primary');
        $this->icon('heroicon-o-plus');
        $this->action(fn (Collection $records, array $data) => $this->addMaterialsToPoints($records, $data));
        $this->slideOver();
    }

    public function getForm(Form $form): ?Form
    {
        return $form->schema(AddPoint::getMaterialsFields())
            ->columns(2);
    }

    private function addMaterialsToPoints(Collection $records, array $data): void
    {
        $materialsToAttach = [];
        collect($data)
            ->values()
            ->map(function ($materials) use (&$materialsToAttach) {
                foreach ($materials as $key => $value) {
                    if ($value) {
                        $materialsToAttach[] = $key;
                    }
                }
            });
        $numberOfChangedPoints = 0;
        $records->each(function (Point $record) use ($materialsToAttach , &$numberOfChangedPoints) {
            if ($record->serviceType->can_collect_materials)
            {
                $record->materials()->attach($materialsToAttach);
                $numberOfChangedPoints++;
            }
        });

        if ($numberOfChangedPoints > 0) {
            Notification::make('success')
                ->title(__('map_points.actions.bulk_add_materials.success', ['count' => $numberOfChangedPoints]))
                ->icon('heroicon-o-check')
                ->iconColor('success')
                ->send();
        } else {
            Notification::make('error')
                ->title(__('map_points.actions.bulk_add_materials.error'))
                ->danger()
                ->send();
        }
    }
}
