<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Material;
use App\Models\MaterialCategory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MaterialsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection): void
    {
        MaterialCategory::insert(
            $collection
                ->filter(fn (Collection $row) => $row->get('category_rank'))
                ->map(fn (Collection $row) => [
                    'name' => trim($row->get('material_category_primary')),
                    'position' => (int) $row->get('category_rank'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
                ->sortBy('position')
                ->all()
        );

        $categories = MaterialCategory::pluck('id', 'name');

        $collection
            ->unique('id_material')
            ->each(function (Collection $row) use ($categories) {
                $material = Material::create([
                    'name' => $row->get('material_label'),
                ]);

                $material->categories()->sync(
                    collect([
                        $row->get('material_category_primary'),
                        $row->get('material_category_secondary'),
                    ])
                        ->map(fn ($name) => $categories->get($name))
                        ->filter()
                        ->all()
                );
            });
    }
}
