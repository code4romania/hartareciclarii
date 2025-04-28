<?php

declare(strict_types=1);

namespace App\Filament\Resources\PointResource\Actions\Table;

use App\Filament\Resources\PointResource\Actions\Page\AddPoint;
use App\Models\Point;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Collection;

class BulkRemoveMaterials extends BulkAction
{
    public static function make(?string $name = null): static
    {
        return parent::make('bulkRemoveMaterials');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('map_points.actions.bulk_remove_materials.title'));

        $this->color('danger');
        $this->icon('heroicon-o-minus');
        $this->action(fn (Collection $records, array $data) => $this->removeMaterialsFromPoints($records, $data));
        $this->slideOver();
    }

    public function getForm(Form $form): ?Form
    {
        return $form->schema(AddPoint::getMaterialsFields())
            ->columns(2);
    }

    private function removeMaterialsFromPoints(Collection $records, array $data): void
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
        $records->each(function (Point $record) use ($materialsToAttach, &$numberOfChangedPoints) {
            if ($record->serviceType->can_collect_materials) {
                $record->materials()->detach($materialsToAttach);
                $numberOfChangedPoints++;
            }
        });

        if ($numberOfChangedPoints > 0) {
            Notification::make('success')
                ->title(__('map_points.actions.bulk_remove_materials.success', ['count' => $numberOfChangedPoints]))
                ->icon('heroicon-o-check')
                ->iconColor('success')
                ->send();
        } else {
            Notification::make('error')
                ->title(__('map_points.actions.bulk_remove_materials.error'))
                ->danger()
                ->send();
        }
    }
}
