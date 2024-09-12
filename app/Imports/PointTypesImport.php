<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\ServiceType;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class PointTypesImport implements ToCollection
{
    public function collection(Collection $collection): void
    {
        $serviceTypes = ServiceType::all();

        $collection
            ->each(
                fn (Collection $row) => $serviceTypes
                    ->firstWhere('slug', $row->pull(0))
                    ->pointTypes()
                    ->createMany(
                        $row
                            ->filter()
                            ->map(fn ($name) => [
                                'name' => $name,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ])
                    )
            );
    }
}
