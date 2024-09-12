<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Problem\ProblemType;
use App\Models\ServiceType;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProblemTypesImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection): void
    {
        $serviceTypes = ServiceType::all();

        $collection
            ->reject(fn (Collection $row) => $row->get('parent_id'))
            ->each(function (Collection $row) use ($serviceTypes) {
                $problemType = ProblemType::forceCreate([
                    'code' => Str::after($row->get('id'), 'PRB_'),
                    'name' => $row->get('name'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $ids = $serviceTypes
                    ->whereIn('slug', explode(',', (string) $row->get('service_types')))
                    ->pluck('id')
                    ->all();

                $problemType->serviceTypes()->attach($ids);
            });

        $mainProblemTypes = ProblemType::all();

        $collection
            ->filter(fn (Collection $row) => $row->get('parent_id'))
            ->each(function (Collection $row) use ($mainProblemTypes) {
                ProblemType::forceCreate([
                    'code' => Str::after($row->get('id'), 'PRB_'),
                    'parent_id' => $mainProblemTypes
                        ->firstWhere('code', Str::after($row->get('parent_id'), 'PRB_'))
                        ->id,
                    'name' => $row->get('name'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }
}
